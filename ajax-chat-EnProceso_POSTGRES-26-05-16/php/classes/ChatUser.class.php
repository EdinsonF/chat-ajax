<?php

class ChatUser extends ChatBase{
	
	protected $name = '', $gravatar = '';
	
	public function save(){

		$num=pg_num_rows(pg_query("SELECT usuario FROM usuario 
									WHERE usuario='".pg_escape_string($this->name)."'"));

		if($num>0){

			$num=pg_num_rows(pg_query("SELECT usuario, contrasena FROM usuario 
										WHERE usuario='".pg_escape_string($this->name)."'
										AND contrasena='".pg_escape_string($this->gravatar)."'"));

			if($num>0){
				return $num;
			}else{
				return 0;
			}
			

		}else{

			$strl=pg_query("INSERT INTO usuario (usuario, contrasena, estatus)
				VALUES (
						'".pg_escape_string($this->name)."',
						'".pg_escape_string($this->gravatar)."',
						'ACTIVO'
				)");
		
			return pg_affected_rows($strl);
	}

		}

		
		
	
	public function update(){
		$strl=pg_query("UPDATE webchat_users SET last_activity = NOW() WHERE name='".pg_escape_string($this->name)."' AND gravatar='".pg_escape_string($this->gravatar)."'");
				
	}



	/////---------MI AGREGADO---->>>>>

	public function id_usuarioActivo(){

		$stsql=pg_query("SELECT id_usuario FROM usuario 
						WHERE usuario='".pg_escape_string($this->name)."'
						AND contrasena='".pg_escape_string($this->gravatar)."'");

		$reg=pg_fetch_array($stsql);

		return $reg['id_usuario'];


	}
}

?>