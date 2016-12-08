<?php

require_once('../vendor/autoload.php');

use Http\Client\HttpClient;
use Http\Client\Common\PluginClient;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use CWP\ADS\AbstractApi\Api;
use CWP\ADS\AbstractApi\Factory\FactoryInterface;
use CWP\ADS\AbstractApi\Object\BaseObject;

class OutbrainFactory implements FactoryInterface {
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

        $data = reset($decodedBody);
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

$token = 'MTQ4MDk2ODY2NjA1MjpkNzcxYjk3YzUxZjVhOTBiN2FiNTc1OWE1ZTRjMjJhYWZkOWJhNjUxNWU4OWVlOGMxMDJkZmRlNTZhZjU0NzYzYzYxMDVlZjZlMTM2YmFkMjJjZjdkMDI1MzVlNWNkNTVkMTE1MjMxMjU0ODdjMzUzYWNlMTUwNjE3ODM0NmE4YmQzMmI2NzNjMmUyOGEwMjhlYTZmYzU4NTdkMDY4YjEzMzRkZGQ1ZWViMDEzN2FjNTBhNjdmNjljYWEyYWQxNTYxNDY3Y2I4NGE5Mzk4NGE5ZDBhZGQ4ZGMyOGY0YzljMTBkOGU0M2UxNmZjMzdiNjJkODNhN2RjYWEwZjJhNDQwMWU5OGU3NGVlYTIxMDI4MDQyNTMyYzE2MDNjNGY2ZDkwODJhNzM0ZTRmNGM4ZjI5Yjg4MjRjMGQ0M2YzOTI3NTM1YTBjYTdlMGY5ZjEzNmMzYmFjZDNhZGQwNWE4NmE0NzRmMDUxNTE5YjFkZmU5ZmZiMmVhMmE4NTBhNWNiZjM3MjNlZTNhOTg0OWUxNjQ1MmI3MjM2N2VjZDAxMmFhNDRhMTNkNDEwZjQ3YzA0MDNhNTA3NTg0ZDU5ZWFjODI4MWQxNDUxMGMyNzgwMDJlMjE1MzQyOTM0OTIxYWM0YWY1YTkwODM2ZjEzN2NmNTM0OTVlN2Y5YWZjMGMxMzNmMDp7ImNhbGxlckFwcGxpY2F0aW9uIjoiQW1lbGlhIiwiaXBBZGRyZXNzIjoiMTAuNDAuOTkuMjA1IiwiYnlwYXNzQXBpQXV0aCI6ImZhbHNlIiwidXNlck5hbWUiOiJhZG9wc0B0b2RheXNidXp6LmNvbSIsInVzZXJJZCI6IjEwMzgzOTI3IiwiZGF0YVNvdXJjZVR5cGUiOiJNWV9PQl9DT00ifTpkY2YxMDBjZWYxYmUwZDFhNzg4ODFkNWViMmJkMTJiMGRiNTA1ZjhiNWI5NmY5OTQ5OWExODFkZDBiYjBkZDYzYjJkMzQzYTA3MmU0NjQ5NjFhMjM5M2I5MjZmZDM2MmUxYjA1N2RhYjc5MjA4YTdiZWQ0MzRhYTJjNDJjYWM0OA==';
// Create our base client, and set the adapter
$guzzle = new GuzzleClient(array(
    'base_uri' => 'https://api.outbrain.com/'
));
$client = new GuzzleAdapter($guzzle);

// Add any necessary plugins for the API. We have to pass `Authorization` header,
// with a vlaue of `Bearer $token` for any requests.
$plugins = array();
array_push($plugins, new HeaderDefaultsPlugin(array(
    'OB-TOKEN-V1' => $token
)));
$pluginClient = new PluginClient($client, $plugins);
$factory = new OutbrainFactory();
// Instantiate our API, and make a request
$api = new Api(array(), $pluginClient);
$api->registerFactory($factory);
$res = $api->get('/amplify/v0.1/marketers');
var_dump($res);
