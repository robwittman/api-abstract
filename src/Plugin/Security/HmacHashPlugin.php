<?php

namespace CWP\ADS\AbstractApi\Plugin\Security;

use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Psr7\Uri;

class HmacHashPlugin implements Plugin {
    protected $paramName;
    
    public function __construct($secret, $paramName = 'hmac') {
        $this->secret = $secret;
        $this->paramName = $paramName;
    }

    public function handleRequest(RequestInterface $request, callable $next, callable $first) {
        $uri = $request->getUri();
        if(empty($uri->getQuery())) {
            return $next($request);
        }
        $params = array();
        parse_str($uri->getQuery(), $params);
        asort($params);
        $hashString = implode("&", $params);
        $hmac = hash_hmac('sha256', $hashString, $this->secret);
        $params[$this->paramName] = $hmac;
        $uri = new Uri(join("?", array($uri->getPath(), http_build_query($params))));

        return $next($request->withUri($uri));
    }
}
