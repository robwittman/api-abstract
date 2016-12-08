<?php

require_once('../vendor/autoload.php');

use Http\Client\HttpClient;
use Http\Client\Common\PluginClient;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request;

use CWP\ADS\AbstractApi\Api;

$token = 'revcontent-access-token';
// Create our base client, and set the adapter
$guzzle = new GuzzleClient(array(
    'base_uri' => 'https://api.revcontent.io'
));
$client = new GuzzleAdapter($guzzle);

// Add any necessary plugins for the API. We have to pass `Authorization` header,
// with a vlaue of `Bearer $token` for any requests.
$plugins = array();
array_push($plugins, new HeaderDefaultsPlugin(array(
    'Authorization' => "Bearer {$token}"
)));
$pluginClient = new PluginClient($client, $plugins);

// Instantiate our API, and make a request
$api = new Api(array(), $pluginClient);
$res = $api->get('/stats/api/v1.0/boosts', array('debug' => 'testing'));
var_dump($res);
