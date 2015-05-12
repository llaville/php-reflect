<?php
/**
 * Manage cache of parsing results
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
 * Manage cache of parsing results
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha1
 */
class Cache extends BaseApi
{
    /**
     * Clear cache (any adapter and backend).
     *
     * @param string $source Path to the data source or its alias.
     * @param string $alias  If set, the source refers to its alias.
     *
     * @return int Number of entries cleared in cache
     * @throws \Exception        if data source provider is unknown
     * @throws \RuntimeException if cache plugin is not installed
     */
    public function clear($source, $alias = null)
    {
        $source = trim($source);
        if ($alias) {
            $alias = $source;
        } else {
            $alias = false;
        }
        return $this->request('cache/clear', 'POST', array($source, $alias));
    }
}
