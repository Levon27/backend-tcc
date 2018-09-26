<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->map(['POST'],'/sensor', function (Request $request, Response $response, array $args) {
	
	if (!isset($_SESSION["id"])){
		echo "Não logou";
		return $response->withStatus(401); //usuario noa logado
	}
	
	
	require_once("db.php");
	$registro = json_decode($request->getBody(),true);
	
	$id_sensor = $registro['id_sensor'];
	$equipamento = $registro['equipamento'];
	$prop = $_SESSION['id'];
	
	$query = $pdo->prepare('INSERT INTO sensor (id_sensor,equipamento,proprietario) SELECT * FROM ( SELECT ?,?,?) AS temp WHERE NOT EXISTS (SELECT id_sensor FROM sensor WHERE id_sensor=?)');
	$query->execute([$id_sensor,$equipamento,$prop,$id_sensor]);
	
	return $response;
});