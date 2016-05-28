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
		$strl=pg_query("UPDATE usuario SET estatus = 'ACTIVO' 
						WHERE usuario='".pg_escape_string($this->name)."'
						AND contrasena='".pg_escape_string($this->gravatar)."'");			
	}


	public function CerrarSesion(){
		$strl=pg_query("UPDATE usuario SET estatus = 'INACTIVO' 
						WHERE usuario='".pg_escape_string($this->name)."'
						AND contrasena='".pg_escape_string($this->gravatar)."'");
		return pg_affected_rows($strl);			
	}



	/////---------MI AGREGADO---->>>>>

	public function idUsuario_Patrocinador(){

		$stsql=pg_query("SELECT id_usuario FROM usuario 
						WHERE usuario='".pg_escape_string($this->name)."'
						AND contrasena='".pg_escape_string($this->gravatar)."'");

		$reg=pg_fetch_array($stsql);
		return $reg['id_usuario'];

	}
}

?>