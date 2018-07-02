<?php
	$host = '127.0.0.1';
	$db   = 'teste_sustek';
	$user = 'root';
	$pass = '';
	$charset = 'utf8mb4';
	
	$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
	$pdo = new PDO($dsn, $user, $pass);
?>