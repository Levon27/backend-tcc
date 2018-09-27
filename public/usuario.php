<?php

// if(!isset($_SESSION)) { 
    // session_start();
// } 
// session_start();

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// header("Content-Type: application/json");
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers: Content-Type");

$app->map(['POST'],'/usuario', function (Request $request, Response $response, array $args) {
	// echo "criando usuario... <br>";
	
	require_once("db.php");
	$registro = json_decode($request->getBody(),true);
	
	$data_inicial = new DateTime("now", new DateTimeZone('America/Sao_Paulo'));
	$data_inicial = $data_inicial->format('Y-m-d');
	
	$nome = $registro["nome"]; 
	$email = $registro["email"];
	$senha = $registro["senha"];
	$cidade = $registro["cidade"];
	
	$hash_senha = md5($senha);
	echo($hash_senha);
	
	// var_dump ("nome: $nome ");
	// var_dump ("email: $email ");
	// var_dump ("senha: $senha ");
	// var_dump ("cidade: $cidade ");
	
	
	$query = $pdo->prepare("INSERT INTO dados_usuario (nome,cidade,email,senha,hash_senha,data_inicial) SELECT * FROM ( SELECT ?,?,?,?,?,?) AS temp WHERE NOT EXISTS (SELECT email FROM dados_usuario WHERE email=?)");
	$query->execute([$nome,$cidade,$email,$senha,$hash_senha,$data_inicial,$email]);

	// echo "<br> executou query ";
	return $response;
});

$app->map(['POST'],'/login', function (Request $request, Response $response, array $args) {
	require_once("db.php");
	
	if (!empty($_SESSION["id"])){
		// echo ("ja logou");
		return $response->withStatus(200); //usuario ja logado
		
	}
	else{
		echo "nao esta logado <br>";
	}
	
	$registro = json_decode($request->getBody(),true);
	$login = $registro["login"];
	$senha = $registro["pass"];
	$hash_senha = md5($senha);
	$query = $pdo->prepare('SELECT * FROM dados_usuario WHERE email=? AND hash_senha=?');
	$query->execute([$login,$hash_senha]);
	
	if ($data = $query->fetch(PDO::FETCH_ASSOC)){
		$_SESSION['email'] = $data["email"]; //usuario encontrado
		$_SESSION['id'] = $data['id_usuario'];
		$email = $_SESSION['email'];
		$id = $_SESSION['id'];
		echo "Bem-vindo, $email. Seu id é : $id";
	}else 
		return $response->withStatus(401); //login ou senha incorretos
	
	return $response->withStatus(200);

	
});

$app->map(['POST'],'/logout', function (Request $request, Response $response, array $args) {
	//var_dump ($_SESSION);
	if (logado()){
		$id_sessao = $_SESSION["id"];
		echo "Usuario $id_sessao deslogando...";
		session_destroy();	
		return $response->withStatus(200);
	} else {
		echo "ja esta deslogado";
		return $response->withStatus(200);
	}
	//var_dump ($_SESSION["id"]);
	return $response;
});

$app->map(['GET'],'/usuario/sensor', function (Request $request, Response $response, array $args) {
	//Retorna todos os sensores do usuario
	require('db.php');
	
	$id = $_SESSION['id'];
	
	$query = $pdo->prepare('SELECT * FROM sensor WHERE proprietario = ?');
	$query->execute([$id]);
	
	
	if ($sensor = $query->fetchAll()){
		return $response->withJson($sensor,200);
	}else{
		return $response->withStatus(404);
		

	
	}
});