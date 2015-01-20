<?php

namespace Bartlett\Reflect\Api\V3;

use Bartlett\Reflect;
use Bartlett\Reflect\Analyser\AnalyserManager;
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
        $am = $this->registerAnalysers();
        return $am->toArray();
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

        $am = $this->registerAnalysers();

        $analysersAvailable = array();
        foreach ($am->getAnalysers() as $analyser) {
            $analysersAvailable[$analyser->getShortName()] = $analyser;
        }

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
            $reflect->addAnalyser($analysersAvailable[$analyserName]);
        }

        $pm = new PluginManager($this->eventDispatcher);
        if ($this->registerPlugins) {
            $pm->registerPlugins();
        }

        return $reflect->parse($finder);
    }

    protected function registerAnalysers()
    {
        $file = 'Bartlett/CompatInfo/Analyser/CompatibilityAnalyser.php';
        $reflectBaseDir = dirname(dirname(dirname(dirname(dirname(__DIR__)))));

        $baseDir   = dirname(dirname(dirname($reflectBaseDir)));
        $vendorDir = $baseDir . '/vendor';

        $namespaces = array();

        if (file_exists($vendorDir) && is_dir($vendorDir)) {
            // CompatInfo only
            $namespaces['Bartlett\CompatInfo\Analyser']
                = $baseDir . '/src/Bartlett/CompatInfo/Analyser'
            ;
        } else if ($path = stream_resolve_include_path($file)) {
            // CompatInfo only, without composer
            $namespaces['Bartlett\CompatInfo\Analyser'] = dirname($path);
        }

        $am = new AnalyserManager($namespaces);
        $am->registerAnalysers();

        return $am;
    }
}
