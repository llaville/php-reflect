<?php
/**
 * Cache clear console command.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Command;

use Bartlett\Reflect\Plugin\Cache\DefaultCacheStorage;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to clear cached results of a previous analysis run.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 2.3.0
 */
class CacheClearCommand extends ProviderCommand
{
    protected function configure()
    {
        $this
            ->setName('cache:clear')
            ->setDescription('Clear cache (any adapter and backend).')
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'Path to the data source or its alias'
            )
            ->addOption(
                'alias',
                null,
                InputOption::VALUE_NONE,
                'If set, the source refers to its alias'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $var = $this->getApplication()->getJsonConfigFile();

        if (!is_array($var)) {
            throw new \Exception(
                'The json configuration file has an invalid format'
            );
        }

        $source = trim($input->getArgument('source'));
        if ($input->getOption('alias')) {
            $alias = $source;
        } else {
            $alias = false;
        }

        if (is_array($var['source-providers'])) {
            $providers = $var['source-providers'];
        } else {
            $providers = array($var['source-providers']);
        }

        if (is_array($var['plugins'])) {
            $pluginsInstalled = $var['plugins'];
        } else {
            $pluginsInstalled = array($var['plugins']);
        }

        foreach ($providers as $provider) {
            if ($this->findProvider($provider, $source, $alias) === false) {
                continue;
            }
            $entriesCleared = 0;

            foreach ($pluginsInstalled as $pluginInstalled) {
                if (stripos($pluginInstalled['class'], 'cacheplugin') === false) {
                    continue;
                }
                // cache plugin found

                if (isset($pluginInstalled['options']['adapter'])) {
                    $adapterClass = $pluginInstalled['options']['adapter'];
                } else {
                    // default cache adapter
                    $adapterClass = 'DoctrineCacheAdapter';
                }
                if (strpos($adapterClass, '\\') === false) {
                    // add default namespace
                    $adapterClass = "Bartlett\\Reflect\\Cache\\" . $adapterClass;
                }
                if (!class_exists($adapterClass)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Adapter "%s" cannot be loaded.',
                            $adapterClass
                        )
                    );
                }

                if (!isset($pluginInstalled['options']['backend']['class'])) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Backend is missing for %s',
                            $adapterClass
                        )
                    );
                }
                $backendClass = $pluginInstalled['options']['backend']['class'];

                if (!class_exists($backendClass)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Backend "%s" cannot be loaded.',
                            $backendClass
                        )
                    );
                }
                $rc = new \ReflectionClass($backendClass);

                if (isset($pluginInstalled['options']['backend']['args'])
                    && is_array($pluginInstalled['options']['backend']['args'])
                ) {
                    $args = $pluginInstalled['options']['backend']['args'];
                } else {
                    $args = array();
                }

                for ($a = 0, $max = count($args); $a < $max; $a++) {
                    // Expands variable from Environment on each argument
                    $count = preg_match_all("/%{([^}]*)}/", $args[$a], $reg);
                    for ($i = 0 ; $i < $count ; $i++) {
                        $val = getenv($reg[1][$i]);
                        if ($val) {
                            $args[$a] = str_replace(
                                $reg[0][$i],
                                $val,
                                $args[$a]
                            );
                        }
                    }
                }
                $backend = $rc->newInstanceArgs($args);

                $cacheAdapter = new $adapterClass($backend);

                $cache = new DefaultCacheStorage($cacheAdapter);

                $entriesCleared = $cache->purge($this->source[0]);

                $output->writeln(
                    sprintf(
                        '<info>%d cache entries cleared</info>',
                        $entriesCleared
                    )
                );
                return;
            }
        }

        throw new \Exception(
            'None data source matching'
        );
    }
}
