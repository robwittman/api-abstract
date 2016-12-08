<?php

namespace CWP\ADS\AbstractApi\Factory;

use CWP\ADS\AbstractApi\Object\BaseObject;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class BaseFactory implements FactoryInterface {

    /**
     * Take a psr7 response, and create
     * @param  ResponseInterface $response
     * @param  RequestInterface $request
     * @param  Api $api
     * @return mixed
     */
    public function createObjectFromResponse(ResponseInterface $response, RequestInterface $request, $api = null) {
        $decodedBody = json_decode($response->getBody());
        $mapped = array();

        $data = $decodedBody->data;
        if(is_array($data)) {
            foreach($data as $obj) {
                array_push($mapped, new BaseObject($obj));
            }

            return $mapped;
        } else {
            return new BaseObject($data);
        }
    }
}
