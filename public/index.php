<?php

if(!isset($_SESSION)) { 
    session_start(); 
} 

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

$app = new \Slim\App;

require_once("popula_db.php");
require 'usuario.php';
require 'funcao_logado.php';
require 'sensor.php';
$app->map(['GET','POST'],'/hello/{name}', function (Request $request, Response $response, array $args) {
	$nome = $args['name'];
	
	echo "Ola, $nome";
});

$app->map(['POST'],'/hello', function (Request $request, Response $response, array $args) {
	echo "funfou o post" ;
});

$app->map(['GET'],'/{id}/{corrente}/{tensao}/{potencia}', function (Request $request, Response $response, array $args){
	//ROTA DESATUALIZADA COM O BD
	require_once("db.php");
	
	$req = array();
	
	$id = $request->getAttribute('id');
	$c = $request->getAttribute('corrente');
	$v = $request->getAttribute('tensao');
	$p = $request->getAttribute('potencia');
	
	echo "id: $id, corrente: $c, tensão: $v, potência: $p \n";
	
	array_push($req,$id,$c,$v,$p);
	
	$query = $pdo->prepare('INSERT INTO dados_consumo VALUES (?,?,?,?)');
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

	// curl_setopt($ch, CURLOPT_URL,'http://api.openweathermap.org/data/2.5/weather?id=3449324&APPID=8013fa9bd47fd985c1fd6854c635c5e8');
	$ch = curl_init('http://api.openweathermap.org/data/2.5/weather?id=5913490&APPID=8013fa9bd47fd985c1fd6854c635c5e8');
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); // Do not send to screen

	$resp_parseada = json_decode(curl_exec($ch),true);
	curl_close($ch);

	// var_dump($resp_parseada);
	
	$temp = $resp_parseada['main']['temp']-273; //substrai 273 para transformar a temperatura de Kelvin para Celsius
	//$data_hora = " ' " . $d .' '. $h . " ' " ;
	
	$data_hora = new DateTime("now", new DateTimeZone('America/Sao_Paulo'));
	$data_hora = $data_hora->format('Y-m-d H:i:s');
	
	echo "<br> id: $id, corrente: $c, tensão: $v, potência: $p hora medicao:$data_hora  Temperatura: $temp <br>";
	
	array_push($req,$id,$c,$v,$p,$temp,$data_hora);
	
	$query = $pdo->prepare('INSERT INTO dados_consumo (id_sensor,corrente,tensao,potencia,temp,data_hora) VALUES (?,?,?,?,?,?)');
	$query->execute($req);
	
});

$app->map(['DELETE'],'/session', function (Request $request, Response $response, array $args) { 
	session_destroy();
	unset($_SESSION);
});

$app->run();
?>