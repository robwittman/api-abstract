<?php

namespace CWP\ADS\AbstractApi\Factory;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface FactoryInterface {
    /**
     * Take a psr7 response, and create
     * @param  ResponseInterface $response
     * @param  RequestInterface $request
     * @param  Api $api
     * @return mixed
     */
    public function createObjectFromResponse(ResponseInterface $response, RequestInterface $request, $api = null);
}
