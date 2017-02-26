<?php
/**
 * Collect and analyse metrics of parsing results.
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

namespace Bartlett\Reflect\Api\V3;

use Bartlett\Reflect;
use Bartlett\Reflect\Analyser\AnalyserManager;
use Bartlett\Reflect\Plugin\PluginManager;

/**
 * Collect and analyse metrics of parsing results.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha1
 */
class Analyser extends Common
{
    public function __call($name, $args)
    {
        if ('list' == $name) {
            return $this->dir();
        }
    }

    /**
     * List all analysers available.
     *
     * @return array
     */
    public function dir()
    {
        $am = $this->registerAnalysers();
        return $am->toArray();
    }

    /**
     * Analyse a data source and display results.
     *
     * @param string   $source    Path to the data source or its alias
     * @param array    $analysers One or more analyser to perform (case insensitive).
     * @param mixed    $alias     If set, the source refers to its alias
     * @param string   $format    If set, convert result to a specific format.
     * @param mixed    $filter    (optional) Function used to filter results
     *
     * @return array metrics
     * @throws \InvalidArgumentException if an analyser required is not installed
     */
    public function run($source, array $analysers, $alias, $format, $filter)
    {
        $finder = $this->findProvider($source, $alias);

        if ($finder === false) {
            throw new \RuntimeException(
                'None data source matching'
            );
        }

        if ($filter === false) {
            // filter feature is not possible on reflection:* commands
            $filter = function ($data) {
                return $data;
            };
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

        $response = $reflect->parse($finder);

        $response = $filter($response);

        if (!empty($format)) {
            $transformMethod = sprintf('transformTo%s', ucfirst($format));
            if (!method_exists($this, $transformMethod)) {
                throw new \InvalidArgumentException(
                    'Could not render result in this format (not implemented).'
                );
            }
            $response = $this->$transformMethod($response);
        }

        return $response;
    }

    /**
     * Registers all analysers
     * bundled with distribution and declared by user in the JSON config file.
     *
     * @return AnalyserManager
     */
    protected function registerAnalysers()
    {
        $file = 'Bartlett/CompatInfo/Analyser/CompatibilityAnalyser.php';
        $reflectBaseDir = dirname(dirname(dirname(dirname(dirname(__DIR__)))));

        $baseDir   = dirname(dirname(dirname($reflectBaseDir)));
        $vendorDir = $baseDir . '/vendor';

        $baseAnalyser   = $baseDir . '/src/Bartlett/CompatInfo/Analyser';
        $vendorAnalyser = $vendorDir . '/bartlett/php-compatinfo/src/Bartlett/CompatInfo/Analyser';

        $namespaces = array();

        if (file_exists($vendorDir) && is_dir($vendorDir)
            && (file_exists($baseAnalyser) || file_exists($vendorAnalyser))
        ) {
            // CompatInfo only
            $namespaces['Bartlett\CompatInfo\Analyser']
                = file_exists($baseAnalyser) ? $baseAnalyser : $vendorAnalyser
            ;
        } elseif ($path = stream_resolve_include_path($file)) {
            // CompatInfo only, without composer
            $namespaces['Bartlett\CompatInfo\Analyser'] = dirname($path);
        }

        $am = new AnalyserManager($namespaces);
        $am->registerAnalysers();

        return $am;
    }
}
