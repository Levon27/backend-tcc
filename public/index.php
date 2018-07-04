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

$app->map(['GET'],'/dados/{id}/{corrente}/{tensao}/{potencia}', function (Request $request, Response $response, array $args) {
	/*
	if(!isset($_SESSION)) { 
		session_start();
		$_SESSION['req1'] = FALSE;
		$_SESSION['req2'] = FALSE;
		$_SESSION['req3'] = FALSE;		
	} 
	*/
	
	require_once("db.php");
	
	$req = array();
	
	$id = $request->getAttribute('id');
	$c = $request->getAttribute('corrente');
	$v = $request->getAttribute('tensao');
	$p = $request->getAttribute('potencia');
	
	echo "id: $id, corrente: $c, tensão: $v, potência: $p \n";
	
	array_push($req,$id,$c,$v,$p);
	
	$query = $pdo->prepare('INSERT INTO teste_sustek VALUES (?,?,?,?)');
	
	if (empty($_SESSION["req1"])){
			//first request
			$_SESSION["req1"] = $req;
			echo "primeira req";
		} else if (empty($_SESSION["req2"])){
			//second request
			$_SESSION["req2"] = $req;
			if ($_SESSION["req1"]==$_SESSION["req2"]){			
				$query->execute($req);
				echo "duas req iguais, inserir no banco";
				session_destroy();
				return $response->withStatus(201);
			}
		} else if (empty($_SESSION["req3"])){
			//compare them here
			$_SESSION["req3"] = $req;
			
			if ($_SESSION["req3"] == $_SESSION["req1"] || $_SESSION["req3"]==$_SESSION["req2"]){
				$query->execute($req);
				echo "requicao aceita, inserir no banco";
				session_destroy();
				return $response->withStatus(201);
			}
			
			
		}
	
});
$app->run();
?>