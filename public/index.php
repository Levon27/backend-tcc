<?php
session_start();

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

$app->map(['GET'],'/{id}/{corrente}/{tensao}/{potencia}', function (Request $request, Response $response, array $args){
		
	require_once("db.php");
	
	$req = array();
	
	$id = $request->getAttribute('id');
	$c = $request->getAttribute('corrente');
	$v = $request->getAttribute('tensao');
	$p = $request->getAttribute('potencia');
	
	echo "id: $id, corrente: $c, tensão: $v, potência: $p \n";
	
	array_push($req,$id,$c,$v,$p);
	
	$query = $pdo->prepare('INSERT INTO teste_sustek VALUES (?,?,?,?)');
	$query->execute($req);
	
		
});
$app->map(['POST'],'/dados', function (Request $request, Response $response, array $args) {
	require_once("db.php");
	
	$req = array();
	
	$parsed_body = json_decode($request->getBody(),true);
	
	$id = $parsed_body['id_sensor'];
	$c = $parsed_body['corrente'];
	$v = $parsed_body['tensao'];
	$p = $parsed_body['potencia'];
	$h = $parsed_body['hora'];
	$d = $parsed_body['data'];
	
	//$data_hora = " ' " . $d .' '. $h . " ' " ;
	$data_hora =  $d .' '. $h;
	
	echo "id: $id, corrente: $c, tensão: $v, potência: $p data_hora:$data_hora \n";
	
	array_push($req,$id,$c,$v,$p,$data_hora);
	
	$query = $pdo->prepare('INSERT INTO teste_sustek (id_sensor,corrente,tensao,potencia,data_hora) VALUES (?,?,?,?,?)');
	
	$query->execute($req);
	
});

$app->run();
?>