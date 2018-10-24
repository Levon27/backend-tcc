<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

if(!isset($_SESSION)) { 
    session_start(); 
} 
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


// ** alterar os dois de baixo ** //
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
	
	$resp = array("resultado"=>$total);
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

$app->map(['GET'],'/consumo/total/{medidor}/{mes}/{dia}', function (Request $request, Response $response, array $args) {
	require('db.php');
	$mes = $args['mes'];
	$dia = $args['dia'];
	$medidor = $args['medidor'];
	$arr = array();	
	$url = 'http://raul0010.pythonanywhere.com/consulta/'.$medidor;
	$dados = consulta($url);  
	foreach($dados['consulta'] as $valor){
		$mes_medicao = date('m',strtotime($valor['data']));
		$dia_medicao = date('d',strtotime($valor['data']));
		// echo "Dia: $dia_medicao    Mes: $mes_medicao <br>";
		if ($mes_medicao == $mes AND $dia_medicao == $dia)
			array_push($arr,$valor['potencia']);
	}	
	if (array_sum($arr)==0)
		return $response->withStatus(404); //sem dados no banco
	$total = array_sum($arr);
	$resp = array("resultado"=>$total);
	
	return $response->withJson($resp);
});

//consumo de energia total por mês do ano
$app->map(['GET'],'/consumo/anual', function (Request $request, Response $response, array $args) {
	require('db.php');
	$data_atual = new DateTime("now", new DateTimeZone('America/Sao_Paulo'));
	$mes_atual = $data_atual->format('n');
	$ano_atual = $data_atual->format('y');
	$resp = array_fill(0,12,0); //cria um array associativo (tipo um json) com 12 indices
	// $resp = array (0=>0,1=>0,"02"=>0,"03"=>0,"04"=>0,"05"=>0,"06"=>0,"07"=>0,"08"=>0,"09"=>0,"10"=>0,"11"=>0);
	
	$sensores = $_SESSION['sensores'];
	foreach ($sensores as $sensor){
		$url = 'http://raul0010.pythonanywhere.com/consulta/'.$sensor['id_sensor'];
		$dados = consulta($url);
		foreach ($dados['consulta'] as $medicao){
			$ano_medicao = (int) date('y',strtotime($medicao['data']));
			$mes_medicao = (int) date('n',strtotime($medicao['data']));
			// echo $mes_medicao . '/';
			if ($ano_medicao != $ano_atual)
				echo "Ano atual: $ano_atual Ano medicao: $ano_medicao";
				// return $response->withStatus(404);		
			$resp[$mes_medicao-1] += $medicao['potencia']/(60*1000);
		}
	}
	return $response->withJson($resp);
});

$app->map(['GET'],'/gasto', function (Request $request, Response $response, array $args) { //alterar para diario
	require('db.php');
	$dados = array();
	$consumo = 0;
	$data_atual = new DateTime("now", new DateTimeZone('America/Sao_Paulo'));
	$dia_atual = $data_atual->format('d');
	$mes_atual = $data_atual->format('m');
	$sensores = $_SESSION['sensores'];
	foreach($sensores as $sensor){
		$url = 'http://raul0010.pythonanywhere.com/consulta/'.$sensor['id_sensor'];
		$dados = consulta($url);
		foreach ($dados['consulta'] as $medicao){
			// echo $medicao['potencia'] . '/';
			$dia_medicao = (int) date('d',strtotime($medicao['data']));
			$mes_medicao = (int) date('n',strtotime($medicao['data']));
			if ($dia_medicao == $dia_atual and $mes_atual == $mes_medicao)
				$consumo += $medicao['potencia'];/(60*1000); // conusmo estimado para cada entrada de 1 minutos (em kWh)
		}
	}
	$custo = 0.21276+0.27087+0.05;
	$gasto_estimado = $consumo*$custo;
	$resp = array("gasto"=>$consumo);
	// $resp = array("gasto"=>$gasto_estimado);
	
	return $response->withJson($resp);
});
function consulta ($url){
	$ch = curl_init($url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); // Do not send to screen
	$resp_parseada = json_decode(curl_exec($ch),true);
	curl_close($ch);	
	return $resp_parseada;
}