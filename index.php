<?php
require_once './vendor/autoload.php';

use Bambam\Bambam;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

$client = new Bambam('signalization', 'jhlee@habitfactory.co', 'zmffhqjvlfem5^habitfactory');
$client->get('projects')->then(function(Response $res) {
	echo $res->getStatusCode() . "\n";
}, function(RequestException $e) {
	echo $e->getMessage() . "\n";
	echo $e->getRequest()->getMethod();
})->wait();
// EOF
