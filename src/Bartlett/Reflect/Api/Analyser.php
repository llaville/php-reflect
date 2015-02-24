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

namespace Bartlett\Reflect\Api;

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
class Analyser extends BaseApi
{
    /**
     * List all analysers available.
     *
     * @return array
     * @alias  list
     */
    public function dir()
    {
        return $this->request('analyser/list');
    }

    /**
     * Analyse a data source and display results.
     *
     * @param string $source    Path to the data source or its alias
     * @param array  $analysers One or more analyser to perform (case insensitive).
     * @param mixed  $alias     If set, the source refers to its alias
     * @param string $format    If set, convert result to a specific format.
     *
     * @return array metrics
     * @throws \InvalidArgumentException if an analyser required is not installed
     */
    public function run($source, array $analysers, $alias = null, $format = false)
    {
        $source = trim($source);
        if ($alias) {
            $alias = $source;
        } else {
            $alias = false;
        }
        return $this->request('analyser/run', 'POST', array($source, $analysers, $alias, $format));
    }
}
