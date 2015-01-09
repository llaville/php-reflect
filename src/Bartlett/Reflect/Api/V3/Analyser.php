<?php

namespace Bartlett\Reflect\Api\V3;

use Bartlett\Reflect;
use Bartlett\Reflect\Plugin\PluginManager;

class Analyser extends Common
{
    public function __call($name, $args)
    {
        if ('list' == $name) {
            return $this->dir();
        }
    }

    public function dir()
    {
        $namespaces = array(
            'Bartlett\Reflect\Analyser\\' => dirname(dirname(__DIR__)) . '/Analyser',
        );

        $reflectBaseDir = dirname(dirname(dirname(dirname(dirname(__DIR__)))));

        $baseDir   = dirname(dirname(dirname($reflectBaseDir)));
        $vendorDir = $baseDir . '/vendor';

        if (file_exists($vendorDir) && is_dir($vendorDir)) {
            // CompatInfo only
            $namespaces['Bartlett\CompatInfo\Analyser\\']
                = $baseDir . '/src/Bartlett/CompatInfo/Analyser';
        }

        $analysers = array();

        foreach ($namespaces as $ns => $path) {
            if (\Phar::running(false)) {
                $iterator = new \Phar($path);
            } else {
                $iterator = new \DirectoryIterator($path);
            }

            foreach ($iterator as $file) {
                if (fnmatch('*Analyser.php', $file->getPathName())) {
                    $name = basename(str_replace('Analyser.php', '', $file->getPathName()));
                    if ('Abstract' !== $name) {
                        $analysers[strtolower($name)] = $ns . basename($file, '.php');
                    }
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

        $analysersAvailable = $this->dir();

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
