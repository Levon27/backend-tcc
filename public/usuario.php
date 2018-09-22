<?php

if(!isset($_SESSION)) { 
    session_start(); 
} 

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// header("Content-Type: application/json");
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers: Content-Type");

$app->map(['POST'],'/usuario', function (Request $request, Response $response, array $args) {
	//echo "criando usuario...";
	
	require_once("db.php");
	$registro = json_decode($request->getBody(),true);
	
	$data_inicial = new DateTime("now", new DateTimeZone('America/Sao_Paulo'));
	$data_inicial = $data_inicial->format('Y-m-d');
	
	$nome = $registro["nome"]; 
	$email = $registro["email"];
	$senha = $registro["senha"];
	$cidade = $registro["cidade"];
	
	var_dump ("nome: $nome ");
	var_dump ("email: $email ");
	var_dump ("senha: $senha ");
	var_dump ("cidade: $cidade ");
	
	
	$query = $pdo->prepare("INSERT INTO dados_usuario (nome,cidade,email,senha,data_inical) SELECT * FROM ( SELECT ?,?,?,?,?) AS temp WHERE NOT EXISTS (SELECT email FROM dados_usuario WHERE email=?)");
	$query->execute([$nome,$cidade,$email,$senha,$data_inicial,$email]);
	
	return $response;
});

$app->map(['POST'],'/login', function (Request $request, Response $response, array $args) {
	require_once("db.php");
	
	if (!(empty($_SESSION["id"]))){
		echo "ja logou";
		return $response->withStatus(200); //usuario ja logado
		
	}
	
	$registro = json_decode($request->getBody(),true);
	$login = $registro["login"];
	$senha = $registro["pass"];
	
	$query = $pdo->prepare('SELECT * FROM dados_usuario WHERE email=? AND senha=?');
	$query->execute([$login,$senha]);
	
	if ($data = $query->fetch(PDO::FETCH_ASSOC)){
		echo "entrou";
		$_SESSION["id"]  = $data["email"]; //usuario encontrado
	}else 
		return $response->withStatus(401); //login ou senha incorretos
	
	return $response->withStatus(200);

	
});

$app->map(['POST'],'/logout', function (Request $request, Response $response, array $args) {
	//var_dump ($_SESSION);
	if (logado()){
		echo "deslogando...";
		session_destroy();	
		return $response->withStatus(204);
	} else {
		echo "ja esta deslogado";
		return $response->withStatus(200);
	}

	
	//var_dump ($_SESSION["id"]);
	return $response;
});