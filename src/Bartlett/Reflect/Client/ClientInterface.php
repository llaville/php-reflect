<?php

namespace Bartlett\Reflect\Client;

/**
 * Client Interface
 *
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
    public function request($method, $url, array $params = array());
}
