<?php

namespace Bartlett\Reflect\Api\V3;

use Bartlett\Reflect;
use Bartlett\Reflect\Plugin\PluginManager;
use Bartlett\Reflect\Plugin\CachePlugin;

class Analyser extends Common
{
    const ANALYSER_DEFAULT_NAMESPACE = 'Bartlett\Reflect\Analyser\\';

    public function __call($name, $args)
    {
        if ('invoke' == $name) {
        } elseif ('list' == $name) {
            if (count($args) === 0) {
                $path = dirname(dirname(__DIR__)) . '/Analyser';
            } else {
                $path = $args[0];
            }
            return $this->dir($path);
        }
    }

    public function __invoke($arg)
    {
        if (!file_exists($arg)) {
            throw new \BadMethodCallException(
                sprintf('Directory %s does not exist.', $arg)
            );
        }
        return $this->dir($arg);
    }

    public function dir($path)
    {
        if (\Phar::running(false)) {
            $iterator = new \Phar($path);
        } else {
            $iterator = new \DirectoryIterator($path);
        }

        $analysers = array();
        foreach ($iterator as $file) {
            if (fnmatch('*Analyser.php', $file->getPathName())) {
                $name = basename(str_replace('Analyser.php', '', $file->getPathName()));
                if ('Abstract' !== $name) {
                    $analysers[strtolower($name)] = self::ANALYSER_DEFAULT_NAMESPACE . basename($file, '.php');
                }
            }
        }

        return $analysers;
    }

    public function run($source, array $analysers, $alias)
    {
        $finder = $this->findProvider($source, $alias);

        if ($finder === false) {
            throw new \RuntimeException(
                'None data source matching'
            );
        }

        $reflect = new Reflect();
        $reflect->setEventDispatcher($this->eventDispatcher);
        $reflect->setDataSourceId($this->dataSourceId);

        $path = dirname(dirname(__DIR__)) . '/Analyser';
        $analysersAvailable = $this->dir($path);

        // attach valid analysers only
        foreach ($analysers as $analyserName) {
            if (!array_key_exists(strtolower($analyserName), $analysersAvailable)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        '"%s" Analyser is not installed.',
                        $analyserName
                    )
                );
            }
            $reflect->addAnalyser(
                new $analysersAvailable[strtolower($analyserName)]
            );
        }

        $pm = new PluginManager($this->eventDispatcher);
        if ($this->registerPlugins) {
            $pm->registerPlugins();
        }

        return $reflect->parse($finder);
    }
}
