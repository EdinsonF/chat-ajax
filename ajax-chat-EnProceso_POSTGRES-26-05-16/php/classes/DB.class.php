<?php

class DB {
	private static $instance;
	private $MySQLi;
	
	private function __construct(array $dbOptions){

				$cadenaConexion="host 		='".$dbOptions['db_host']."' 
								 port 		='".$dbOptions['db_port']."' 
								 dbname 	='".$dbOptions['db_name']."' 
								 user 		='".$dbOptions['db_user']."' 
								 password 	='".$dbOptions['db_pass']."'";

    			$conn = pg_connect($cadenaConexion) or die("Error en la Conexión: PORQUERIA".pg_last_error());


//var_dump($dbOptions);
		// if($conn==0){

		//     }
		// else{


  //        pg_set_charset("utf8");
		// 	}

		
	}
	
	public static function conectar(array $dbOptions){

		self::$instance = new self($dbOptions);
		
	}
	
	// public static function getMySQLiObject(){
	// 	return self::$instance->MySQLi;
	// }
	
	// public static function query($q){
	// 	return self::$instance->MySQLi->query($q);
	// }
	
	// public static function esc($str){
	// 	return self::$instance->MySQLi->real_escape_string(htmlspecialchars($str));
	// }
}

?>