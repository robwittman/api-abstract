<?php

namespace CWP\ADS\AbstractApi;

use CWP\ADS\AbstractApi\Factory\FactoryInterface;
use CWP\ADS\AbstractApi\Exception\AbstractApiSDKException;
use CWP\ADS\AbstractApi\Exception\ClientException;
use CWP\ADS\AbstractApi\Exception\ServerException;

use Http\Client\Common\PluginClient;
use GuzzleHttp\Psr7\Request;

class Api {

    /**
     * Configuration settings
     * @var array
     */
    protected $_config;

    /**
     * Configured API Client
     * @var PluginClient
     */
    protected $_client;

    /**
     * Factory for creating objects from responses
     * @var FactoryInterface
     */
    protected $_factory = null;

    /**
     * Create a new API handle
     * @param array $config API specific config
     * @param $client
     * @param Factory $factory  Factory for creating objects from responses
     */
    public function __construct(array $config, $client) {
        $this->_config = $config;
        $this->_client = $client;
    }

    /**
     * Set the factory to process responses with
     * @param FactoryInterface $factory
     */
    public function registerFactory(FactoryInterface $factory) {
        $this->_factory = $factory;
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
     * @return mixed
     */
    public function request($method, $endpoint, array $params = array(), $returnRaw = false) {
        if(empty($params)) {
            $url = $endpoint;
        } else {
            $url = join("?", array($endpoint, http_build_query($params)));
        }

        $request = new Request($method, $url);
        $response = $this->sendRequest($request);

        if(!is_null($this->getFactory()) && !$returnRaw) {
            return $this->getFactory()->createObjectFromResponse($response, $request, $this);
        } else {
            return $response;
        }
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
        return $this->_client;
    }

    public function getFactory() {
        return $this->_factory;
    }
}
