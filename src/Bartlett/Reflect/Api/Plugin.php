<?php declare(strict_types=1);

/**
 * Manage plugins
 *
 * PHP version 7
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect\Api;

/**
 * Identify all plugins available
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 3.0.0-alpha1
 */
class Plugin extends BaseApi
{
    /**
     * List all plugins installed.
     *
     * @return array
     * @alias  list
     */
    public function dir()
    {
        return $this->request('plugin/list');
    }
}
