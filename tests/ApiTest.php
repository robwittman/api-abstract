<?php

namespace CWP\ADS\AbstractApi\Tests;

use CWP\ADS\AbstractApi\Api;
use CWP\ADS\AbstractApi\Factory\BaseFactory;
use CWP\ADS\AbstractApi\Exception\ClientException;
use CWP\ADS\AbstractApi\Exception\ServerException;

use Http\Mock\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Request;

class ApiTest extends TestCase {
    public function testClient() {
        $client = new Client();
        $api = new Api(array(), $client);
        $this->assertTrue($api->getClient() instanceof Client);
    }

    public function testFactory() {
        $factory = new BaseFactory('src');
        $client = new Client();
        $api = new Api(array(), $client);
        $api->registerFactory($factory);
        $this->assertTrue($api->getFactory() instanceof BaseFactory);
    }

    public function testClientException() {
        $client = new Client();
        $exception = new ClientException('Whoops!');
        $client->addException($exception);
        $api = new Api(array(), $client);

        $request = new Request('GET', 'https://google.com');
        $this->setExpectedException('\CWP\ADS\AbstractApi\Exception\ClientException');
        $returnedResponse = $client->sendRequest($request);
    }

    public function testServerException() {
        $client = new Client();
        $exception = new ServerException('Whoops!');
        $client->addException($exception);
        $api = new Api(array(), $client);

        $request = new Request('GET', 'https://google.com');
        $this->setExpectedException('\CWP\ADS\AbstractApi\Exception\ServerException');
        $returnedResponse = $client->sendRequest($request);
    }

    public function testResponse() {
        $client = new Client();
        $response = $this->createMock('Psr\Http\Message\ResponseInterface');
        $client->addResponse($response);
        $api = new Api(array(), $client);
        $request = new Request('GET', 'https://google.com');

        $res = $api->sendRequest($request);
        $this->assertInstanceOf(ResponseInterface::class, $res);
    }
}
