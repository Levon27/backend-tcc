<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
/*
 consumo total/mes (todos medidores)
X media consumo/mes (todos medidores) 
X mes com maior consumo (todos medidores) 
X mes com menor consumo (todos medidores) 
X consumo por medidor/mes			
X consumo por medidor desde sempre - servidor python
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



$app->map(['GET'],'/consumo/maior', function (Request $request, Response $response, array $args) {
	require('db.php');
	$arr = array();
	
	$dados = consulta('http://raul0010.pythonanywhere.com/consulta/*');
	
	foreach($dados['consulta'] as $valor)
		array_push($arr,$valor['potencia']);
	
	$maior = max($arr);
	$indice = array_search($maior,$arr);
	
	$resp=$dados['consulta'][$indice];
	
	return $response->withJson($resp);
});

$app->map(['GET'],'/consumo/menor', function (Request $request, Response $response, array $args) {
	require('db.php');
	$arr = array();
	
	$dados = consulta('http://raul0010.pythonanywhere.com/consulta/*');
	
	foreach($dados['consulta'] as $valor)
		array_push($arr,$valor['potencia']);
	
	$menor = min($arr);
	$indice = array_search($menor,$arr);
	
	$resp=$dados['consulta'][$indice];
	
	return $response->withJson($resp);
});

$app->map(['GET'],'/consumo/total/{mes}', function (Request $request, Response $response, array $args) {
	require('db.php');
	$arr = array();
	$mes = $args['mes'];
	$dados = consulta('http://raul0010.pythonanywhere.com/consulta/*');
	
	foreach($dados['consulta'] as $valor){
		$mes_medicao = date('m',strtotime($valor['data']));
		if ($mes_medicao == $mes)
			array_push($arr,$valor['potencia']);
	}
	$total = array_sum($arr);
	
	$resp= array("resultado"=>$total);
	
	return $response->withJson($resp);
});

//media conusmo medidor/mes
$app->map(['GET'],'/consumo/media/{medidor}/{mes}', function (Request $request, Response $response, array $args) {
	require('db.php');
	$mes = $args['mes'];
	$medidor = $args['medidor'];
	$arr = array();	
	$url = 'http://raul0010.pythonanywhere.com/consulta/'.$medidor;
	$dados = consulta($url);  
	foreach($dados['consulta'] as $valor){
		$mes_medicao = date('m',strtotime($valor['data']));
		if ($mes_medicao == $mes)
			array_push($arr,$valor['potencia']);
	}
	
	if (array_sum($arr)==0)
		return $response->withStatus(404); //sem dados no banco
	
	$media = array_sum($arr)/count($arr);
	$resp = array("resultado"=>$media);
	
	return $response->withJson($resp);
});


// consumo por medidor/mes	
$app->map(['GET'],'/consumo/total/{medidor}/{mes}', function (Request $request, Response $response, array $args) {
	require('db.php');
	$mes = $args['mes'];
	$medidor = $args['medidor'];
	$arr = array();	
	$url = 'http://raul0010.pythonanywhere.com/consulta/'.$medidor;
	$dados = consulta($url);  
	foreach($dados['consulta'] as $valor){
		$mes_medicao = date('m',strtotime($valor['data']));
		if ($mes_medicao == $mes)
			array_push($arr,$valor['potencia']);
	}
	
	if (array_sum($arr)==0)
		return $response->withStatus(404); //sem dados no banco
	
	$media = array_sum($arr);
	$resp = array("resultado"=>$media);
	
	return $response->withJson($resp);
});


function consulta ($url){
	$ch = curl_init($url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); // Do not send to screen
	$resp_parseada = json_decode(curl_exec($ch),true);
	curl_close($ch);	
	return $resp_parseada;
}