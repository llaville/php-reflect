<?php

namespace Bartlett\Reflect\Api;

use Bartlett\Reflect\Client\ClientInterface;
use Bartlett\Reflect\Event\AbstractDispatcher;

abstract class BaseApi extends AbstractDispatcher
{
    protected $client;

    private $token;

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
     * Performs the request
     *
     * @param string $url
     * @param string $method
     * @param array  $params
     */
    protected function request($url, $method = 'GET', array $params = array())
    {
        $this->client->setEventDispatcher($this->getEventDispatcher());

        $response = $this->client->request($method, $url, $params);
        return $response;
    }
}
