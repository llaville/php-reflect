<?php
/**
 * Analyser manager
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  GIT: $Id$
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Analyser;

use Bartlett\Reflect\Environment;
use Bartlett\Reflect\Api\V3\Config;

/**
 * Analyser manager
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha2+1
 */
class AnalyserManager
{
    protected $analysers = array();

    /**
     * Initializes analyser manager
     *
     * @param array $namespaces (optional) other analysers location to grab
     */
    public function __construct(array $namespaces = null)
    {
        $defaultNamespace = array(
            __NAMESPACE__ => __DIR__,
        );
        if (isset($namespaces)) {
            $namespaces = array_merge($defaultNamespace, $namespaces);
        } else {
            $namespaces = $defaultNamespace;
        }

        foreach ($namespaces as $ns => $path) {
            if (\Phar::running(false)) {
                $iterator = new \Phar($path);
            } else {
                $iterator = new \DirectoryIterator($path);
            }

            foreach ($iterator as $file) {
                if (fnmatch('*Analyser.php', $file->getPathName())) {
                    $name = basename(str_replace('Analyser.php', '', $file->getPathName()));
                    if (strpos($name, 'Abstract') !== 0) {
                        $class    = rtrim($ns, '\\') . '\\' . basename($file, '.php');
                        $analyser = new $class;

                        if ($analyser instanceof AnalyserInterface) {
                            $this->addAnalyser($analyser);
                        }
                    }
                }
            }
        }
    }

    /**
     * Loads all analysers declared in the JSON config file.
     *
     * @return void
     */
    public function registerAnalysers()
    {
        $jsonFile = Environment::getJsonConfigFilename();
        if (!$jsonFile) {
            return;
        }

        $config = new Config;
        $var    = $config->validate($jsonFile);

        foreach ($var['analysers'] as $analyser) {
            if (class_exists($analyser['class'])) {
                $analyser = new $analyser['class'];

                if ($analyser instanceof AnalyserInterface) {
                    $this->addAnalyser($analyser);
                }
            }
        }
    }

    /**
     * Adds an analyser
     *
     * @param AnalyserInterface $analyser Plugin instance
     *
     * @return void
     */
    public function addAnalyser(AnalyserInterface $analyser)
    {
        $this->analysers[] = $analyser;
    }

    /**
     * Gets all currently active analyser instances
     *
     * @return array analysers
     */
    public function getAnalysers()
    {
        return $this->analysers;
    }

    /**
     * Array representation of all analysers registered.
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();

        foreach ($this->analysers as $analyser) {
            $array[$analyser->getShortName()] = get_class($analyser);
        }
        return $array;
    }
}
