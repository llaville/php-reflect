<?php

declare(strict_types=1);

/**
 * Collect and analyse metrics of parsing results.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
*/

namespace Bartlett\Reflect\Api;

/**
 * Collect and analyse metrics of parsing results.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
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
     * @param mixed  $filter    Resource that provide a closure to filter results.
     *
     * @return array metrics
     * @throws \InvalidArgumentException if an analyser required is not installed
     * @throws \RuntimeException         if filter provided is not a closure
     */
    public function run($source, array $analysers, $alias = null, $format = false, $filter = false)
    {
        $source = trim($source);
        if ($alias) {
            $alias = $source;
        } else {
            $alias = false;
        }

        if ($filter instanceof \Closure
            || $filter === false
        ) {
            $closure = $filter;
        } else {
            if ($filterRes = stream_resolve_include_path($filter)) {
                include_once $filterRes;
            }
            if (!isset($closure) || !is_callable($closure)) {
                throw new \RuntimeException(
                    sprintf(
                        'Invalid filter provided by file "%s"',
                        $filterRes ? : $filter
                    )
                );
            }
        }

        return $this->request('analyser/run', 'POST', array($source, $analysers, $alias, $format, $closure));
    }
}
