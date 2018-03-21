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

$app->get('/',function(){
	echo '<h1>Welcome to Api SocialNetwork Althreeus</h1>';
	echo "<table>";
	echo "<tr><td>get </td><td>/categorys</td><td>Lista de categorias</td></tr>";
	echo "<tr><td>Devuelve id,name</td></tr>";
	echo "<tr><td>get </td><td>/posts</td><td>Lista de posts</td></tr>";
	echo "<tr><td>Devuelve id,idtopic,iduser,nick,content,date</td></tr>";
	echo "<tr><td>get </td><td>/technologys</td><td>Lista de tecnologias</td></tr>";
	echo "<tr><td>Devuelve id,name,logo,color</td></tr>";
	echo "<tr><td>get </td><td>/topics</td><td>Lista de topics</td></tr>";
	echo "<tr><td>Devuelve id,iduser,nick,idtechnology,nametechnology,idcategory,namecategory,date,name</td></tr>";
	echo "<tr><td>get </td><td>/topic/{id}</td><td>Topic por id</td></tr>";
	echo "<tr><td>Devuelve id,iduser,nick,idtechnology,nametechnology,idcategory,namecategory,date,name</td></tr>";
	echo "<tr><td>get </td><td>/post/{id}</td><td>Post por id</td></tr>";
	echo "<tr><td>Devuelve id,idtopic,iduser,nick,content,date</td></tr>";
	echo "<tr><td>get </td><td>/posts/technology/{id}</td><td>Listado de posts por id de tecnologia</td></tr>";
	echo "<tr><td>Devuelve id,idtopic,iduser,nick,content,date</td></tr>";
	echo "<tr><td>get </td><td>/user/{id}</td><td>User por id</td></tr>";
	echo "<tr><td>Devuelve id,nick,password,correo,idgit,nickgit</td></tr>";
	echo "<tr><td>get </td><td>/user?nick=AlvaroAlmonacidRojo&password=1234</td><td>User por nick y password</td></tr>";
	echo "<tr><td>Devuelve id,nick,password,correo,idgit,nickgit</td></tr>";
	echo "<br>";
	echo "<tr><td>post </td><td>/post</td><td>Pasar idtopic, iduser y content</td></tr>";
	echo "<tr><td>Devuelve id,idtopic,iduser,content,date</td></tr>";
	echo "<tr><td>post </td><td>/user</td><td>Pasar nick, password, correo y nickgit</td></tr>";
	echo "<tr><td>Devuelve id,nick,password,correo,nickgit</td></tr>";
	echo "<tr><td>post </td><td>/technology</td><td>Pasar name</td></tr>";
	echo "<tr><td>Devuelve id,name</td></tr>";
	echo "<tr><td>post </td><td>/category</td><td>Pasar name</td></tr>";
	echo "<tr><td>Devuelve id,name</td></tr>";
	echo "<tr><td>post </td><td>/topic</td><td>Pasar iduser,idtechnology,idcategory,name</td></tr>";
	echo "<tr><td>Devuelve id,iduser,idtechnology,idcategory,date,name</td></tr>";
	echo "</table>";
});


//GETS
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
	$ordenSql = "select * from technology ORDER BY name";
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
	$ordenSql = "SELECT t.id,iduser,u.nick,idtechnology,te.name as nametechnology,idcategory,c.name as namecategory,c.id,te.id, u.id,t.date, t.name FROM topic t, technology te, category c, user u WHERE (idtechnology = te.id) AND (c.id = idcategory) AND (u.id = iduser)";
	$statement = $conn->prepare($ordenSql);
	$statement->execute();

	$salida = $statement->fetchAll(PDO::FETCH_ASSOC);
	$statement = null;
	return $response->withStatus(200)
	                ->withHeader('Content-Type', 'application/json')
	                ->write(json_encode(["topics"=>$salida]));
});

//Todos los posts
$app->get('/posts', function ($request, $response, $args) use ($conn){
	$ordenSql = "select p.id, p.idtopic, p.iduser,u.nick, p.content, date, u.id from post p, user u where p.iduser = u.id";
	$statement = $conn->prepare($ordenSql);
	$statement->execute();
	$salida = $statement->fetchAll(PDO::FETCH_ASSOC);
	$statement = null;
	return $response->withStatus(200)
	                ->withHeader('Content-Type', 'application/json')
	                ->write(json_encode(["posts"=>$salida]));
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

//Usuario por nick y password
$app->get('/user', function ($request, $response, $args) use ($conn){
	$nick = $request->getParam('nick');
	$password = $request->getParam('password');
	$ordenSql = "select * from user where nick=:nick AND password=:password";
	$statement = $conn->prepare($ordenSql);
	$statement->bindParam(':nick', $nick, PDO::PARAM_STR);
	$statement->bindParam(':password', $password, PDO::PARAM_STR);
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
	$ordenSql = "select p.id, p.idtopic, p.iduser,u.nick, p.content, date, u.id from post p, user u where p.id=:id AND p.iduser = u.id";
	$statement = $conn->prepare($ordenSql);
	$statement->bindParam(':id', $telefono, PDO::PARAM_INT);
	$statement->execute();
	$salida = $statement->fetch(PDO::FETCH_ASSOC);
	$statement = null;
	return $response->withStatus(200)
	                ->withHeader('Content-Type', 'application/json')
	                ->write(json_encode(["post"=>$salida]));
});

//Todos los posts por tecnologia
$app->get('/posts/technology/{idtechnology}', function ($request, $response, $args) use ($conn){
	$idtechnology = $args['idtechnology'];
	$ordenSql = "select p.id, p.idtopic, p.iduser,u.nick, p.content, t.date, u.id , t.idtechnology,te.id, te.name from post p, topic t, technology te, user u where (t.idtechnology=:id AND te.id = :id) AND p.iduser = u.id";
	$statement = $conn->prepare($ordenSql);
	$statement->bindParam(':id', $idtechnology, PDO::PARAM_INT);
	$statement->execute();
	$salida = $statement->fetchAll(PDO::FETCH_ASSOC);
	$statement = null;
	return $response->withStatus(200)
	                ->withHeader('Content-Type', 'application/json')
	                ->write(json_encode(["posts"=>$salida]));
});

//Topic por id
$app->get('/topic/{id}', function ($request, $response, $args) use ($conn){
	$id = $args['id'];
	$ordenSql = "select t.id,iduser,u.nick,idtechnology,te.name as nametechnology,idcategory,c.name as namecategory,c.id,te.id, u.id,t.date from topic t, technology te, category c, user u where (idtechnology = te.id) AND (c.id = idcategory) AND (u.id = iduser) AND t.id= :id";
	$statement = $conn->prepare($ordenSql);
	$statement->bindParam(':id', $id, PDO::PARAM_INT);
	$statement->execute();
	$salida = $statement->fetch(PDO::FETCH_ASSOC);
	$statement = null;
	return $response->withStatus(200)
	                ->withHeader('Content-Type', 'application/json')
	                ->write(json_encode(["topic"=>$salida]));
});


//POSTS
//Insertar un post
$app->post('/post', function ($request, $response, $args) use ($conn) {


	$idtopic = $request->getParam('idtopic');
	$iduser = $request->getParam('iduser');
	$content = $request->getParam('content');
	$date = date("Y-m-d H:i:s");
	if (!isset($idtopic) || !isset($iduser) || !isset($content) || !isset($date)){
		$body = $request->getBody();
		$jsonobj = json_decode($body);
		if ($jsonobj != null) {
			$idtopic = $jsonobj->{'idtopic'};
			$iduser = $jsonobj->{'iduser'};
			$content = $jsonobj->{'content'};
			$date = date("Y-m-d H:i:s");
		}
	}
	try {
		if (!isset($idtopic) || !isset($iduser) || !isset($content) || !isset($date)){
			$salida = "No data";
		} else {
			$ordenSql = "INSERT INTO post(idtopic,iduser,content,date) values(:idtopic,:iduser,:content,:date)";
			$statement = $conn->prepare($ordenSql);
			$statement->bindParam(':idtopic', $idtopic, PDO::PARAM_INT);
			$statement->bindParam(':iduser', $iduser, PDO::PARAM_INT);
			$statement->bindParam(':content', $content, PDO::PARAM_INT);
			$statement->bindParam(':date', $date, PDO::PARAM_STR);
			$conn->beginTransaction();
			$statement->execute();
			$conn->commit();
			$post = ["idtopic"=>$idtopic,"iduser"=>$iduser, "content"=>$content,"date"=>$date];
		}
	}catch (PDOException $e) {
		return $response->withStatus(500)
		                ->withHeader('Content-Type', 'application/json')
		                ->write(json_encode(["msg"=>"Violada Primary key..."]));
	} finally {$statement = null;}
	return $response->withStatus(200)
	                ->withHeader('Content-Type', 'application/json')
	                ->write(json_encode(["post"=>$post]));
});

//Añadir un usuario
$app->post('/user', function ($request, $response, $args) use ($conn) {


	$nick = $request->getParam('nick');
	$password = $request->getParam('password');
	$correo = $request->getParam('correo');
	$nickgit = $request->getParam('nickgit');
	if (!isset($nick) || !isset($password) || !isset($correo) || !isset($nickgit)){
		$body = $request->getBody();
		$jsonobj = json_decode($body);
		if ($jsonobj != null) {
			$nick = $jsonobj->{'nick'};
			$password = $jsonobj->{'password'};
			$correo = $jsonobj->{'correo'};
			$nickgit = $jsonobj->{'nickgit'};
		}
	}
	try {
		if (!isset($nick) || !isset($password) || !isset($correo) || !isset($nickgit)){
			$salida = "No data";
		} else {
			$ordenSql = "INSERT INTO user(nick,password,correo,nickgit) values(:nick,:password,:correo,:nickgit)";
			$statement = $conn->prepare($ordenSql);
			$statement->bindParam(':nick', $nick, PDO::PARAM_STR);
			$statement->bindParam(':password', $password, PDO::PARAM_STR);
			$statement->bindParam(':correo', $correo, PDO::PARAM_STR);
			$statement->bindParam(':nickgit', $nickgit, PDO::PARAM_STR);
			$conn->beginTransaction();
			$statement->execute();
			$iduser = $conn->lastInsertId();
			$conn->commit();

			$user = ["id"=>$iduser,"nick"=>$nick,"password"=>$password, "correo"=>$correo,"nickgit"=>$nickgit];
		}
	}catch (PDOException $e) {
		return $response->withStatus(500)
		                ->withHeader('Content-Type', 'application/json')
		                ->write(json_encode(["msg"=>"Violada Primary key..."]));
	} finally {$statement = null;}
	return $response->withStatus(200)
	                ->withHeader('Content-Type', 'application/json')
	                ->write(json_encode(["user"=>$user]));
});

//Añadir una tecnologia
$app->post('/technology', function ($request, $response, $args) use ($conn) {


	$name = $request->getParam('name');

	if (!isset($name)){
		$body = $request->getBody();
		$jsonobj = json_decode($body);
		if ($jsonobj != null) {
			$name = $jsonobj->{'name'};

		}
	}
	try {
		if (!isset($name)){
			$salida = "No data";
		} else {
			$ordenSql = "INSERT INTO technology(name) values(:name)";
			$statement = $conn->prepare($ordenSql);
			$statement->bindParam(':name', $name, PDO::PARAM_STR);
			$conn->beginTransaction();
			$statement->execute();
			$idtechnology = $conn->lastInsertId();
			$conn->commit();

			$technology = ["id"=>$idtechnology,"name"=>$name];
		}
	}catch (PDOException $e) {
		return $response->withStatus(500)
		                ->withHeader('Content-Type', 'application/json')
		                ->write(json_encode(["msg"=>"Violada Primary key..."]));
	} finally {$statement = null;}
	return $response->withStatus(200)
	                ->withHeader('Content-Type', 'application/json')
	                ->write(json_encode(["technology"=>$technology]));
});


//Añadir una categoria
$app->post('/category', function ($request, $response, $args) use ($conn) {


	$name = $request->getParam('name');

	if (!isset($name)){
		$body = $request->getBody();
		$jsonobj = json_decode($body);
		if ($jsonobj != null) {
			$name = $jsonobj->{'name'};

		}
	}
	try {
		if (!isset($name)){
			$salida = "No data";
		} else {
			$ordenSql = "INSERT INTO category(name) values(:name)";
			$statement = $conn->prepare($ordenSql);
			$statement->bindParam(':name', $name, PDO::PARAM_STR);
			$conn->beginTransaction();
			$statement->execute();
			$idcategory = $conn->lastInsertId();
			$conn->commit();

			$category = ["id"=>$idcategory,"name"=>$name];
		}
	}catch (PDOException $e) {
		return $response->withStatus(500)
		                ->withHeader('Content-Type', 'application/json')
		                ->write(json_encode(["msg"=>"Violada Primary key..."]));
	} finally {$statement = null;}
	return $response->withStatus(200)
	                ->withHeader('Content-Type', 'application/json')
	                ->write(json_encode(["category"=>$category]));
});

//Añadir un topic
$app->post('/topic', function ($request, $response, $args) use ($conn) {


	$iduser = $request->getParam('iduser');
	$idtechnology = $request->getParam('idtechnology');
	$idcategory = $request->getParam('idcategory');
	$date = date("Y-m-d H:i:s");
	$name = $request->getParam('name');
	if (!isset($iduser) || !isset($idtechnology) || !isset($idcategory) || !isset($date) || !isset($name)){
		$body = $request->getBody();
		$jsonobj = json_decode($body);
		if ($jsonobj != null) {
			$iduser = $jsonobj->{'iduser'};
			$idtechnology = $jsonobj->{'idtechnology'};
			$idcategory = $jsonobj->{'idcategory'};
			$date = date("Y-m-d H:i:s");
			$name = $jsonobj->{'name'};
		}
	}
	try {
		if (!isset($iduser) || !isset($idtechnology) || !isset($idcategory) || !isset($date) || !isset($name)){
			$salida = "No data";
		} else {
			$ordenSql = "INSERT INTO topic(iduser, idtechnology, idcategory,date,name) values(:iduser,:idtechnology,:idcategory,:date,:name)";
			$statement = $conn->prepare($ordenSql);
			$statement->bindParam(':iduser', $iduser, PDO::PARAM_INT);
			$statement->bindParam(':idtechnology', $idtechnology, PDO::PARAM_INT);
			$statement->bindParam(':idcategory', $idcategory, PDO::PARAM_INT);
			$statement->bindParam(':date', $date, PDO::PARAM_STR);
			$statement->bindParam(':name', $name, PDO::PARAM_STR);

			$conn->beginTransaction();
			$statement->execute();
			$idtopic = $conn->lastInsertId();
			$conn->commit();

			$topic = ["id"=>$idtopic,"iduser"=>$iduser,"idtechnology"=>$idtechnology, "idcategory"=>$idcategory,"date"=>$date,"name"=>$name];
		}
	}catch (PDOException $e) {
		return $response->withStatus(500)
		                ->withHeader('Content-Type', 'application/json')
		                ->write(json_encode(["msg"=>"Violada Primary key..."]));
	} finally {$statement = null;}
	return $response->withStatus(200)
	                ->withHeader('Content-Type', 'application/json')
	                ->write(json_encode(["topic"=>$topic]));
});
$app->run();