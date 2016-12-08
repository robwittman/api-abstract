<?php

namespace CWP\ADS\AbstractApi\Tests;

use CWP\ADS\AbstractApi\Plugin\Security\HmacHashPlugin;
use GuzzleHttp\Psr7\Request;

class HmacHashPluginTest extends TestCase {
    public function setUp() {
        parent::setUp();
        $this->plugin = new HmacHashPlugin('app_secret');
    }

    public function testWithNoParams() {
        $request = new Request('GET', 'https://google.com');
        $next = function($request) {
            return $request;
        };
        // Run our request through plugin, and set result
        $request = $this->plugin->handleRequest($request, $next, $next);
        $queryString = $request->getUri()->getQuery();
        $vars = array();
        parse_str($queryString, $vars);
        $this->assertFalse(array_key_exists('hmac', $vars));
    }

    public function testWithParams() {
        $request = new Request('GET', 'https://google.com?fb=pe&test=debug');
        $next = function($request) {
            return $request;
        };
        // Run our request through plugin, and set result
        $request = $this->plugin->handleRequest($request, $next, $next);
        $queryString = $request->getUri()->getQuery();
        $vars = array();
        parse_str($queryString, $vars);
        $this->assertNotNull(array_key_exists('hmac', $vars));
        $this->assertEquals($vars['hmac'], $this->generateHmac('app_secret', array('fb' => 'pe', 'test' => 'debug')));
    }

    public function testCustomVariableKey() {
        $plugin = new HmacHashPlugin('app_secret', 'custom');
        $request = new Request('GET', 'https://google.com?fb=pe&test=debug');
        $next = function($request) {
            return $request;
        };
        // Run our request through plugin, and set result
        $request = $plugin->handleRequest($request, $next, $next);
        $queryString = $request->getUri()->getQuery();
        $vars = array();
        parse_str($queryString, $vars);
        $this->assertNotNull(array_key_exists('custom', $vars));
        $this->assertEquals($vars['custom'], $this->generateHmac('app_secret', array('fb' => 'pe', 'test' => 'debug')));
    }

    protected function generateHmac($secret, $params) {
        asort($params);
        $hashString = implode("&", $params);
        return hash_hmac('sha256', $hashString, $secret);
    }
}
