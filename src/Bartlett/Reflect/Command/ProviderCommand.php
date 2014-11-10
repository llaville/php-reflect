<?php

namespace Bartlett\Reflect\Command;

use Bartlett\Reflect\Plugin\Cache\DefaultCacheStorage;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

use Seld\JsonLint\ParsingException;

class ProviderCommand extends Command
{
    protected $source;
    protected $finder;
    protected $cache;
    protected $cachePluginConf;
    protected $logger;
    protected $logPluginConf;

    /**
     * Construct a Reflect base command
     *
     * @param string      $name (optional) Not used, but kept for compatibility with Symfony Command
     * @param Application $app  (optional) Instance of current Application
     *                    for commands to need to know it before configure a new command
     */
    public function __construct($name = null, Application $app = null)
    {
        if ($app) {
            $this->setApplication($app);
        }
        parent::__construct($name);
    }

    /**
     * Checks JSON configuration file before to execute any command.
     *
     * @return int|array exit code if error, json string decoded otherwise
     * @throws \RuntimeException if configuration file does not exists or not readable
     * @throws ParsingException  if configuration file is invalid format
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->hasArgument('file') ? $input->getArgument('file') : null;

        try {
            $out = $this->getJsonConfigFile($file);

        } catch (\RuntimeException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $out = 1;

        } catch (ParsingException $e) {
            $fmt = $this->getApplication()->getHelperSet()->get('formatter');

            $output->writeln(
                $fmt->formatBlock(
                    explode("\n", $e->getMessage()),
                    'error'
                )
            );
            $out = 1;
        }
        return $out;
    }

    /**
     * Gets the contents of a JSON configuration file.
     *
     * @param string $file (optional) Path to a JSON file
     *
     * @return array
     * @throws \RuntimeException if configuration file does not exists or not readable
     * @throws ParsingException  if configuration file is invalid format
     */
    public function getJsonConfigFile($file = null)
    {
        $env = $this->getApplication()->getEnv();

        if (is_null($file)) {
            $file = getenv($env->getEnv());
        }

        $json = $env->validateSyntax($file);

        $var = json_decode($json, true);

        if (!is_array($var['source-providers'])) {
            $var['source-providers'] = array($var['source-providers']);
        }
        if (!is_array($var['analysers'])) {
            $var['analysers'] = array($var['analysers']);
        }
        if (!is_array($var['plugins'])) {
            $var['plugins'] = array($var['plugins']);
        }
        return $var;
    }

    /**
     * Find any provider that match optional criteria $source or $alias
     *
     * @param array  $provider    Current provider to lookup
     * @param string $source      Data source path
     * @param string $alias       Alias of a data source
     * @param bool   $constraints (optional) With or without defined constraints.
     *                            Default with constraints.
     *
     * @return bool TRUE if provider found, FALSE otherwise
     */
    protected function findProvider($provider, $source, $alias, $constraints = true)
    {
        if (!isset($provider['in'])) {
            // this source provider is incomplete: missing "in" key
            return false;
        }
        $in = trim($provider['in']);
        if (empty($in)) {
            // this source provider is incomplete: empty "in" key
            return false;
        }
        $src = explode(' as ', $in);
        $src = array_map('trim', $src);

        if ($source) {
            if (!empty($alias) && count($src) < 2) {
                // searching on alias, which is not provided
                return false;
            }
            $i = empty($alias) ? 0 : 1;
            // search on data source path ($i = 0) or alias ($i = 1)
            if ($src[$i] !== $source) {
                return false;
            }
        }

        if (substr($src[0], 0, 1) == '.') {
            // relative local file
            $src[0] = realpath($src[0]);

            if (PATH_SEPARATOR == ';') {
                // normalizes path to unix format
                $src[0] = str_replace(DIRECTORY_SEPARATOR, '/', substr($src[0], 2));
            }
        }

        // found it
        $finder = new Finder();
        $finder->files()
            ->in($src[0])
        ;

        if (count($src) == 1) {
            // if alias not provided, give empty value
            $src[] = '';
        }

        $this->source = $src;
        $this->finder = $finder;

        if (!$constraints) {
            return true;
        }

        $constraints = array(
            'exclude',                  // Exclude directories
            'name', 'notName',          // File name constraints
            'path', 'notPath',          // Path constraints
            'size',                     // File size constraints
            'date',                     // File date constraints
            'depth',                    // Directory depth constraints
            'contains', 'notContains',  // File contents constraints
        );
        foreach ($constraints as $constraint) {
            if (isset($provider[$constraint])) {
                if (is_array($provider[$constraint])) {
                    $args = $provider[$constraint];
                } else {
                    $args = array($provider[$constraint]);
                }
                foreach ($args as $arg) {
                    $finder->{$constraint}($arg);
                }
            }
        }
        return true;
    }

    /**
     * Find if the cachePlugin is installed or not
     *
     * @param array $plugins Plugins list declared in json configuration file.
     *
     * @return bool TRUE if cache installed, FALSE otherwise
     */
    protected function findCachePlugin($plugins)
    {
        if (is_array($plugins)) {
            $pluginsInstalled = $plugins;
        } else {
            $pluginsInstalled = array($plugins);
        }

        foreach ($pluginsInstalled as $pluginInstalled) {
            if (stripos($pluginInstalled['class'], 'cacheplugin') === false) {
                continue;
            }
            // cache plugin found
            $this->cachePluginConf = $pluginInstalled;

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
                for ($i = 0; $i < $count; $i++) {
                    if ($reg[1][$i] == 'HOME') {
                        $reg[1][$i] = defined('PHP_WINDOWS_VERSION_BUILD')
                            ? 'USERPROFILE' : 'HOME';
                    }
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

            $this->cache = new DefaultCacheStorage($cacheAdapter);

            return true;
        }
        return false;
    }

    /**
     * Find if the logPlugin is installed or not
     *
     * @param array $plugins Plugins list declared in json configuration file.
     *
     * @return bool TRUE if installed, FALSE otherwise
     */
    protected function findLogPlugin($plugins)
    {
        if (is_array($plugins)) {
            $pluginsInstalled = $plugins;
        } else {
            $pluginsInstalled = array($plugins);
        }

        foreach ($pluginsInstalled as $pluginInstalled) {
            if (stripos($pluginInstalled['class'], 'logplugin') === false) {
                continue;
            }
            // log plugin found
            $this->logPluginConf = $pluginInstalled;

            if (!isset($pluginInstalled['options']['logger']['class'])) {
                throw new \InvalidArgumentException(
                    'Logger is undefined.'
                );
            }
            $loggerClass = $pluginInstalled['options']['logger']['class'];

            if (!class_exists($loggerClass)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Logger "%s" cannot be loaded.',
                        $loggerClass
                    )
                );
            }

            if (isset($pluginInstalled['options']['conf'])) {
                if (!is_array($pluginInstalled['options']['conf'])) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'LogPlugin configuration options are invalid. ' .
                            'Expected array, got %s',
                            gettype($pluginInstalled['options']['conf'])
                        )
                    );
                }
            } else {
                $pluginInstalled['options']['conf'] = array();
            }
            $this->logPluginConf['options']['conf'] = $pluginInstalled['options']['conf'];

            $this->logger = new $loggerClass;

            return true;
        }
        return false;
    }
}
