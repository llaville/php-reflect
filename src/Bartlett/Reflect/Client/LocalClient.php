<?php

namespace Bartlett\Reflect\Client;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Client for the local file system.
 *
 */
class LocalClient implements ClientInterface
{
    private $namespace;
    private $eventDispatcher;
    private $registerPlugins;

    /**
     * @param string $url Base of Api Endpoint
     */
    public function __construct($url = 'Bartlett\Reflect\Api\V3')
    {
        $this->namespace = $url;
    }

    public function activatePlugins($register)
    {
        $this->registerPlugins = (bool) $register;
    }

    /**
     * @inheritdoc
     */
    public function request($method, $url, array $params = array())
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
            $response = call_user_func_array(array($api, $methodName), $params);

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
