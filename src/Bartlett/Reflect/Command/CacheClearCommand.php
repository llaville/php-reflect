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
        $var = $this->getApplication()->getEnv()->getJsonConfigFile();

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

        foreach ($providers as $provider) {
            if ($this->findProvider($provider, $source, $alias) === false) {
                continue;
            }
            if ($this->findCachePlugin($var['plugins'])) {
                $entriesCleared = $this->cache->purge($this->source[0]);

                $output->writeln(
                    sprintf(
                        '<info>%d cache entries cleared</info>',
                        $entriesCleared
                    )
                );
                return;
            }
            break;
        }

        throw new \Exception(
            'None data source matching'
        );
    }
}
