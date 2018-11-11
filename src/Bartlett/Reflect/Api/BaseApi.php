<?php
/**
 * Common code to API requests
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

use Bartlett\Reflect\Client\ClientInterface;
use Bartlett\Reflect\Event\AbstractDispatcher;

/**
 * Common code to API requests
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha1
 */
abstract class BaseApi extends AbstractDispatcher
{
    protected $client;

    private $token;
    private $registerPlugins = true;

    /**
     * Initialize any API requests
     *
     * @param ClientInterface $client
     * @param string          $token
     * @param string          $appName
     */
    public function __construct(ClientInterface $client, $token = null)
    {
        $this->client = $client;
        $this->token  = $token;
    }

    /**
     * Allows to disable all plugins declared in the JSON config file
     *
     * @param bool $register Activate (default) or not, all plugins declared
     *
     * @return void
     * @disabled
     */
    public function activatePlugins($register)
    {
        $this->registerPlugins = (bool) $register;
    }

    /**
     * Performs the request
     *
     * @param string $url
     * @param string $method
     * @param array  $params
     */
    protected function request($url, $method = 'GET', array $params = array())
    {
        $this->client->setEventDispatcher($this->getEventDispatcher());
        $this->client->activatePlugins($this->registerPlugins);

        $response = $this->client->request($method, $url, $params);
        return $response;
    }
}
