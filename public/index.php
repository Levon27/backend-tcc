<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
$app = new \Slim\App;

$app->map(['GET','POST'],'/hello/{name}', function (Request $request, Response $response, array $args) {
	$nome = $args['name'];
	
	echo "Ola, $nome";
});

$app->map(['GET'],'/dados/{id}/{corrente}/{tensao}/{potencia}', function (Request $request, Response $response, array $args) {
	require_once("db.php");
	
	$id = $request->getAttribute('id');
	$c = $request->getAttribute('corrente');
	$v = $request->getAttribute('tensao');
	$p = $request->getAttribute('potencia');
	
	echo "id: $id, corrente: $c, tensão: $v, potência: $p";
	
	$query = $pdo->prepare('INSERT INTO teste_sustek VALUES (?,?,?,?)');
	$query->execute([$id,$c,$v,$p]);
});
$app->run();
?>