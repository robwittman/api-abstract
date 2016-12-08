<?php

namespace CWP\ADS\AbstractApi\Tests;

use CWP\ADS\AbstractApi\Factory\RouteBasedFactory;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use CWP\ADS\AbstractApi\Tests\Factory\TestClass;

class RouteBasedFactoryTest extends TestCase {
    public function setUp() {
        $this->factory = new RouteBasedFactory(array(
            '/amplify\/v0.1\/marketers/' => 'list<\CWP\ADS\AbstractApi\Tests\Factory\TestClass>'
        ));
    }

    public function testRouteCheck() {
        $request = new Request('GET', 'http://google.com/amplify/v0.1/marketers');
        $response = new Response(200, array(), '{"marketers": [{"id": "00f4b02153ee75f3c9dc4fc128ab041962","name": "my marketer","enabled": true,"lastModified": "2014-04-20 14:52:13","creationTime": "2013-06-19 21:17:11","blockedSites": {}},{"id": "0095b3a4811c42fe570d1e730e7286adb4","name": "my other marketer","enabled": false,"lastModified": "2014-08-05 14:52:13","creationTime": "2013-01-04 21:17:11","blockedSites": {}}],"count": 2}');

        $res = $this->factory->createObjectFromResponse($response, $request);
        var_dump($res);
    }
}
