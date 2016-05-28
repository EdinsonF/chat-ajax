<?php

/* Database Configuration. Add your details below */

$dbOptions = array(
	'db_host' => 'localhost',
	'db_user' => 'postgres',
	'db_pass' => 'root',
	'db_name' => 'chat',
	'db_port' => '5432'
);

/* Database Config End */


error_reporting(E_ALL ^ E_NOTICE);

require "classes/DB.class.php";
require "classes/Chat.class.php";
require "classes/ChatBase.class.php";
require "classes/ChatLine.class.php";
require "classes/ChatUser.class.php";
require "classes/ChatGrupo.class.php";

session_name('webchat');
session_start();

if(get_magic_quotes_gpc()){
	
	// If magic quotes is enabled, strip the extra slashes
	array_walk_recursive($_GET,create_function('&$v,$k','$v = stripslashes($v);'));
	array_walk_recursive($_POST,create_function('&$v,$k','$v = stripslashes($v);'));
}

try{
	
	// Connecting to the database
	DB::conectar($dbOptions);
	
	$response = array();
	
	// Handling the supported actions:
	
	switch($_GET['action']){
		
		case 'login':
			$response = Chat::login($_POST['name'],$_POST['email']);
		break;
		
		case 'checkLogged':
			$response = Chat::checkLogged();
		break;
		
		case 'logout':
			$response = Chat::logout();
		break;
		
		case 'submitChat':
			$response = Chat::submitChat($_POST['chatText'],$_POST['id_grupo']);
		break;
		
		case 'getUsers':
			$response = Chat::getUsers($_GET['id_grupo']);
		break;
		
		case 'getChats':
			$response = Chat::getChats($_GET['lastID'], $_GET['id_grupo']);
		break;

		case 'crearGrupo_addUsuaurio':
			$response = Chat::CrearGroup_AddUsser($_POST['addUsuario'],$_POST['grupo']);
		break;

		case 'CargarMisGrupos':
			$response = Chat::CargarMisGrupos();
		break;
		
		default:
			throw new Exception('Wrong action');
	}
	
	echo json_encode($response);
}
catch(Exception $e){
	die(json_encode(array('error' => $e->getMessage())));
}

?>