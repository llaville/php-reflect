<?php

namespace Bartlett\Reflect;

use Bartlett\Reflect\Client\ClientInterface;
use Bartlett\Reflect\Client\LocalClient;

/**
 * Client for interacting with the API
 *
 */
class Client
{
    const API_V3 = 'http://php5.laurent-laville.org/reflect/api/v3/';

    private $client;
    private $token;

    /**
     * Initialize a client for interacting with the API
     *
     * @param ClientInterface $client
     * @param string          $url
     */
    public function __construct(ClientInterface $client = null, $url = self::API_V3)
    {
        $this->initializeClient($url, $client);
    }

    /**
     * Returns an Api
     *
     * @param string $name Api method to perform
     *
     * @return Api
     * @throws \InvalidArgumentException
     */
    public function api($name)
    {
        $classes = array(
            'Bartlett\Reflect\Api\\' => array(
                'Analyser',
                'Cache',
                'Config',
                'Plugin',
                'Reflection',
            ),
            'Bartlett\CompatInfo\Api\\' => array(
                'Reference'
            ),
        );

        $class = false;

        foreach ($classes as $ns => $basename) {
            if (in_array(ucfirst($name), $basename)) {
                $class = $ns . ucfirst($name);
                break;
            }
        }

        if (!$class || !class_exists($class)) {
            throw new \InvalidArgumentException(
                sprintf('Unknown Api "%s" requested', $name)
            );
        }
        if ($this->client instanceof LocalClient) {
            $this->client->setNamespace($ns . 'V3');
        }
        return new $class($this->client, $this->token);
    }

    /**
     * Authorizes an Api
     *
     * @param $token
     */
    public function authorize($token)
    {
        $this->token = $token;
    }

    /**
     * Initializes a http or local client
     *
     * @param string          $url    Base URL of endpoints
     * @param ClientInterface $client (optional) client to use
     *
     * @return void
     */
    private function initializeClient($url, ClientInterface $client = null)
    {
        if ($client) {
            $this->client = $client;
        } else {
            $this->client = new LocalClient();
        }
    }
}
