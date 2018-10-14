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
require 'func_mail.php';

/*
X consumo total/mes (todos medidores)
X media consumo/mes (todos medidores)
X mes com maior consumo (todos medidores)
X mes com menor consumo (todos medidores)
consumo por medidor/mes
X consumo por medidor desde sempre
*/


// QUERIES

// consumo total/mes (todos medidores)
//SELECT SUM(potencia),YEAR(data_hora) AS ano,MONTH(data_hora) AS mes FROM `dados_consumo` WHERE MONTH(data_hora) = #MES# GROUP BY YEAR(data_hora),MONTH(data_hora)

// media consumo/mes (todos medidores)
//SELECT AVG(potencia),YEAR(data_hora) AS ano,MONTH(data_hora) AS mes FROM `dados_consumo` WHERE MONTH(data_hora) = #MES# GROUP BY YEAR(data_hora),MONTH(data_hora)

// mes com maior/menor consumo (todos medidores)
// SELECT MAX/MIN(consumo) FROM (SELECT SUM(potencia) AS consumo,YEAR(data_hora) AS ano,MONTH(data_hora) AS mes FROM `dados_consumo` AS consumo_mes GROUP BY YEAR(data_hora),MONTH(data_hora)) AS maior_consumo

// consumo por medidor desde sempre
// SELECT SUM(potencia),id_sensor FROM dados_consumo GROUP BY id_sensor

$app->map(['GET','POST'],'/hello/{name}', function (Request $request, Response $response, array $args) {
	$nome = $args['name'];
	
	echo "Ola, $nome";
});

$app->map(['GET'],'/', function (Request $request, Response $response, array $args) use ($app){
	return $response->withRedirect('http://u643580869.hostingerapp.com/index.html');
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

$app->map(['POST'],'/email', function (Request $request, Response $response, array $args) {
	//nome,sobrenome, e-mail, telefone, mensagem
	if(!isset($_SESSION["id"])){
		echo "Não logou";
		return $response->withStatus(401); //usuario noa logado
	}
	
	$request = json_decode($request->getBody(),true);
	
	$msg = $request['msg'];
	$email = $request['email'];
	$nome = $request['nome'];
	$sobrenome =  $reqeust['sobrenome'];
	$telefone =  $reqeust['tel'];
	
	envia_email($email,$nome,$msg);
	
	return $response->withStatus(200);
	
});

$app->run();

?>