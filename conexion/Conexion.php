<?php
/**
 * Created by PhpStorm.
 * User: Alvaro
 * Date: 26/02/2018
 * Time: 9:22
 */

class Conexion {

	public static function getPDO(){
		$dbhost = "localhost";
		$dbuser = "root";
		$dbpass = "";
		$dbname = "socialnetwork";
		$dns = "mysql:host={$dbhost};dbname={$dbname}";
		try{
			$pdo=new PDO($dns, $dbuser, $dbpass,array(PDO::ATTR_PERSISTENT=>true));
			$pdo->exec("SET NAMES 'utf8'");
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $pdo;
		}catch(PDOException $e) {
			echo $e->getMessage();
		}finally {
			$pdo = NULL;
		}
	}
}