<?php
/**
 * Created by PhpStorm.
 * User: Alvaro
 * Date: 26/02/2018
 * Time: 9:17
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
require_once 'conexion/Conexion.php';

$app = new \Slim\App;
$conn = Conexion::getPDO();

//Todos los posts
$app->get('/posts', function ($request, $response, $args) use ($conn){
	$ordenSql = "select * from post ORDER BY date";
	$statement = $conn->prepare($ordenSql);
	$statement->execute();
	$salida = $statement->fetchAll(PDO::FETCH_ASSOC);
	$statement = null;
	return $response->withStatus(200)
	                ->withHeader('Content-Type', 'application/json')
	                ->write(json_encode(["posts"=>$salida]));
});

//Todas las categorías
$app->get('/categorys', function ($request, $response, $args) use ($conn){
	$ordenSql = "select * from category";
	$statement = $conn->prepare($ordenSql);
	$statement->execute();
	$salida = $statement->fetchAll(PDO::FETCH_ASSOC);
	$statement = null;
	return $response->withStatus(200)
	                ->withHeader('Content-Type', 'application/json')
	                ->write(json_encode(["categorys"=>$salida]));
});

//Todas las tecnologias
$app->get('/technologys', function ($request, $response, $args) use ($conn){
	$ordenSql = "select * from technology";
	$statement = $conn->prepare($ordenSql);
	$statement->execute();
	$salida = $statement->fetchAll(PDO::FETCH_ASSOC);
	$statement = null;
	return $response->withStatus(200)
	                ->withHeader('Content-Type', 'application/json')
	                ->write(json_encode(["technologys"=>$salida]));
});

//Todos los topics
$app->get('/topics', function ($request, $response, $args) use ($conn){
	$ordenSql = "select * from topic";
	$statement = $conn->prepare($ordenSql);
	$statement->execute();
	$salida = $statement->fetchAll(PDO::FETCH_ASSOC);
	$statement = null;
	return $response->withStatus(200)
	                ->withHeader('Content-Type', 'application/json')
	                ->write(json_encode(["topics"=>$salida]));
});

//Usuario por id
$app->get('/user/{id}', function ($request, $response, $args) use ($conn){
	$telefono = $args['id'];
	$ordenSql = "select * from user where id=:id";
	$statement = $conn->prepare($ordenSql);
	$statement->bindParam(':id', $telefono, PDO::PARAM_INT);
	$statement->execute();
	$salida = $statement->fetch(PDO::FETCH_ASSOC);
	$statement = null;
	return $response->withStatus(200)
	                ->withHeader('Content-Type', 'application/json')
	                ->write(json_encode(["user"=>$salida]));
});

//Post por id
$app->get('/post/{id}', function ($request, $response, $args) use ($conn){
	$telefono = $args['id'];
	$ordenSql = "select * from post where id=:id";
	$statement = $conn->prepare($ordenSql);
	$statement->bindParam(':id', $telefono, PDO::PARAM_INT);
	$statement->execute();
	$salida = $statement->fetch(PDO::FETCH_ASSOC);
	$statement = null;
	return $response->withStatus(200)
	                ->withHeader('Content-Type', 'application/json')
	                ->write(json_encode(["post"=>$salida]));
});

$app->run();