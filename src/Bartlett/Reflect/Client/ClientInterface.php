<?php declare(strict_types=1);

/**
 * Client Interface
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 */

namespace Bartlett\Reflect\Client;

/**
 * Client Interface
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @since    Class available since Release 3.0.0-alpha2
 */
interface ClientInterface
{
    /**
     * Performs a Request to the API Endpoint
     *
     * @param string $method
     * @param string $url
     * @param array  $params
     */
    public function request(string $method, string $url, array $params = array());
}
