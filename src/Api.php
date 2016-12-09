<?php

namespace CWP\ADS\AbstractApi;

use CWP\ADS\AbstractApi\Factory\FactoryInterface;
use CWP\ADS\AbstractApi\Exception\AbstractApiSDKException;
use CWP\ADS\AbstractApi\Exception\ClientException;
use CWP\ADS\AbstractApi\Exception\ServerException;

use Http\Client\Common\PluginClient;
use GuzzleHttp\Psr7\Request;

use JsonMapper;

class Api {

    /**
     * Configuration settings
     * @var array
     */
    protected $config;

    /**
     * Configured API Client
     * @var PluginClient
     */
    protected $client;

    /**
     * Factory for creating objects from responses
     * @var FactoryInterface
     */
    protected $factory = null;

    /**
     * Mapper to return objects from API responses
     * @var object
     */
    protected $mapper;

    /**
     * Middlewares
     * @var array
     */
    protected $middleware = array(
        'request' => null,
        'response' => null
    );

    /**
     * Create a new API handle
     * @param array $config API specific config
     * @param $client
     * @param Factory $factory  Factory for creating objects from responses
     */
    public function __construct(array $config, $client) {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * Set the factory to process responses with
     * @param FactoryInterface $factory
     */
    public function registerFactory(FactoryInterface $factory) {
        $this->factory = $factory;
    }

    /**
     * Route all HTTP method calls to ::request()
     * @param  string $method
     * @param  array $arguments
     * @return mixed
     */
    public function __call($method, $arguments) {
        array_unshift($arguments, $method);
        return call_user_func_array(array($this, 'request'), $arguments);
    }

    /**
     * Make an actual request against our confiesgured PluginClient
     * @param  string $method
     * @param  string $endpoint
     * @param  array  $params
     * @todo Validate request and response results
     * @return mixed
     */
    public function request($method, $endpoint, array $params = array(), $returnRaw = false) {
        if(empty($params)) {
            $url = $endpoint;
        } else {
            $url = join("?", array($endpoint, http_build_query($params)));
        }

        $request = new Request($method, $url);
        if(!is_null($this->getMiddleware('request'))) {
            $request = call_user_func($this->getMiddleware('request'), $request);
        }

        $response = $this->sendRequest($request);
        if(!is_null($this->getMiddleware('response'))) {
            $response = call_user_func($this->getMiddleware('response'), $request, $response);
        }

        return $response;
    }

    /**
     * Add a middleware to run against requests
     * @param $middleware
     */
    public function addRequestMiddleware($middleware) {
        if(is_callable($middelware)) {
            $this->middleware['request'] = $middleware;
        }
        throw new AbstractApiSDKException("Middleware must be callable");
    }

    /**
     * Add a middleware to run against responses
     * @param $middleware
     */
    public function addResponseMiddleware($middleware) {
        if(is_callable($middelware)) {
            $this->middleware['response'] = $middleware;
        }
        throw new AbstractApiSDKException("Middleware must be callable");
    }

    /**
     * Get all registered middleware
     * @param  string $type 'request|response'
     * @return callable
     */
    public function getMiddleware($type) {
        return $this->middleware[$type];
    }

    /**
     * Set object mapper for API responses
     * @param  JsonMapper $mapper
     * @return
     */
    public function registerObjectMapper(JsonMapper $mapper) {
        $this->mapper = $mapper;
    }

    /**
     * Send a request object to our client
     * @param  Request $request
     * @return Response
     */
    public function sendRequest(Request $request) {
        try {
            $response = $this->getClient()->sendRequest($request);
        } catch( \Http\Server\Exception\HttpException $e) {
            throw new ServerException("Server error occured. {$e->getMessage()}", $e->getCode());
        } catch( \Http\Client\Exception\HttpException $e) {
            throw new ClientException("Client error occured. {$e->getMessage()}", $e->getCode());
        } catch(\Exception $e) {
            throw new AbstractApiSDKException("Unexpected error occured. {$e->getMessage()}", $e->getCode());
        }
        return $response;
    }

    public function getClient() {
        return $this->client;
    }

    public function getFactory() {
        return $this->factory;
    }
}
