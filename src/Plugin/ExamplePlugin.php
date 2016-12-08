<?php

namespace CWP\ADS\AbstractApi\Plugin;

use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;

class ExamplePlugin implements Plugin {

    /**
     * Handles the request and returns the response coming from the next callable.
     *
     * @param RequestInterface $request Request to use.
     * @param callable         $next    Callback to call to have the request, it muse have the request as it first argument.
     * @param callable         $first   First element in the plugin chain, used to to restart a request from the beginning.
     *
     * @return Promise
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first) {
        return $next($request);
    }
}
