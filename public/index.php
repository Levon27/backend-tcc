<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
$app = new \Slim\App;

$app->map(['GET','POST'],'/hello', function (Request $request, Response $response, array $args) {
	$nome = args['name'];
	
	echo "Ola, $nome";
});
$app->run();
?>