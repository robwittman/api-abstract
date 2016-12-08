<?php

namespace CWP\ADS\AbstractApi\Factory;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RouteBasedFactory implements FactoryInterface {

    /**
     * Route based class map
     * @var array
     */
    protected $_routes = array();

    /**
     * Wether we default to BaseObject in the event class doesnt exist
     * @var boolean
     */
    protected $_fallback = true;

    public function __construct($routes = null, $fallback = true) {
        if(!is_null($routes)) {
            $this->_routes = $routes;
        }
        $this->_fallback = $fallback;
    }

    /**
     * Take a psr7 response, and create
     * @param  ResponseInterface $response
     * @param  RequestInterface $request
     * @param  Api $api
     * @return mixed
     */
    public function createObjectFromResponse(ResponseInterface $response, RequestInterface $request, $api = null) {
        $route = $request->getUri()->getPath();
        $body = json_decode($response->getBody());
        if($map = $this->getClassFromRoute($route)) {
            $opts = $this->parseClassMap($map);
            $className = $opts['class'];
            if($opts['is_list']) {
                $mapped = array();
                foreach(reset($body) as $obj) {
                    $res = new $className($obj);
                    array_push($mapped, $res);
                }
                return $mapped;
            } else {
                return new $className($body);
            }
        } else {
            if($this->_fallback) {
                // Default to base object
                return new BaseObject(reset($body));
            } else {
                throw new \Exception("Class map not created yet for {$route}");
            }
        }
    }

    /**
     * Get the object type to return based on route
     * @param  string $route
     * @return string
     */
    public function getClassFromRoute($route) {
        foreach($this->_routes as $key => $value) {
            if(preg_match($key, $route)) {
                return $value;
            }
        }
        return false;
    }

    /**
     * Check if we need to create a list, or single object
     * @param  string $className  Class that was mapped to route
     * @return array
     */
    public function parseClassMap($className) {
        $matches = array();
        $regex = "/^list<(.*?)>/";
        if(preg_match($regex, $className, $matches)) {
            return array(
                'class' => $matches[1],
                'is_list' => true
            );
        } else {
            return array(
                'class' => $className,
                'is_list' => false
            );
        }
    }
}
