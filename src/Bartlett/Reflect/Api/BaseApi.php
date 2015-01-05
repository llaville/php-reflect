<?php

namespace Bartlett\Reflect\Api;

use Bartlett\Reflect\Client\ClientInterface;
use Bartlett\Reflect\Event\AbstractDispatcher;

abstract class BaseApi extends AbstractDispatcher
{
    protected $client;

    private $token;
    private $registerPlugins = true;

    /**
     * @param ClientInterface $client
     * @param string          $token
     */
    public function __construct(ClientInterface $client, $token = null)
    {
        $this->client = $client;
        $this->token  = $token;
    }

    /**
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
