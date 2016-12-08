<?php

namespace CWP\ADS\AbstractApi\Tests;

use CWP\ADS\AbstractApi\Factory\BaseFactory;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class BaseFactoryTest extends TestCase {

    protected $mockResponseData = '{"data" : [{"id" : "123412341234"},{"id" : "4321341234312"}]}';
    protected $singleObject = '{"data" : {"name" : "Test Campaign"}}';

    public function setUp() {
        parent::setUp();
        $this->factory = new BaseFactory('src');
    }

    public function testCreateObjectFromResponse() {
        $request = new Request('GET', 'https://google.com');
        $response = new Response(200, array(), $this->mockResponseData);

        $objects = $this->factory->createObjectFromResponse($response, $request, null);
        $this->assertEquals($objects[0]->id, "123412341234");
    }

    public function testSingleObjectResponse() {
        $request = new Request('GET', 'https://google.com');
        $response = new Response(200, array(), $this->singleObject);

        $object = $this->factory->createObjectFromResponse($response, $request, null);
        $this->assertEquals($object->name, "Test Campaign");
    }
}
