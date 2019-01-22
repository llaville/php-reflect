<?php

declare(strict_types=1);

/**
 * Local Client
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 */

namespace Bartlett\Reflect\Client;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Client for the local file system.
 *
 * @category PHP
 * @package  PHP_Reflect
 * @author   Laurent Laville <pear@laurent-laville.org>
 * @license  https://opensource.org/licenses/BSD-3-Clause The 3-Clause BSD License
 * @link     http://php5.laurent-laville.org/reflect/
 * @since    Class available since Release 3.0.0-alpha2
 */
class LocalClient implements ClientInterface
{
    private $namespace;
    private $eventDispatcher;
    private $registerPlugins;

    /**
     * Initialize the local file system client
     *
     * @param string $url Base of Api Endpoint
     */
    public function __construct($url = 'Bartlett\Reflect\Api\V3')
    {
        $this->setNamespace($url);
    }

    /**
     * Allows to disable all plugins declared in the JSON config file
     *
     * @param bool $register Activate (default) or not, all plugins declared
     *
     * @return void
     */
    public function activatePlugins($register)
    {
        $this->registerPlugins = (bool) $register;
    }

    /**
     * Defines a new namespace of Api
     *
     * @param string $url Base of Api Endpoint
     *
     * @return self for a fuent interface
     */
    public function setNamespace($url)
    {
        $this->namespace = $url;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function request($method, $url, array $params = [])
    {
        $parts = explode('/', $url);

        $className  = $this->namespace . '\\' . ucfirst($parts[0]);
        $methodName = count($parts) > 1 ? $parts[1] : 'invoke';

        $api = new $className;
        $api->setEventDispatcher($this->eventDispatcher);
        $api->activatePlugins($this->registerPlugins);

        try {
            if (!class_exists($className)) {
                throw new \BadFunctionCallException(
                    sprintf('API class endpoint %s does not exist.', $className)
                );
            }
            $response = call_user_func_array(
                array($api, $methodName),
                $params
            );
        } catch (\Exception $e) {
            $response = $e;
        }

        return $response;
    }

    /**
     * Set the EventDispatcher of the request
     *
     * @param EventDispatcherInterface $eventDispatcher Instance of the event
     *        dispatcher
     *
     * @return self for a fuent interface
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        return $this;
    }
}
