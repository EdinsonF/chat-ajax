<?php

/* Chat line is used for the chat entries */

class ChatLine extends ChatBase{
	
	protected $text = '', $author = '', $gravatar = '';
	
	public function save($codigo_grupo){
		$consulta=pg_query("SELECT  inter_usuario_grupo.id_inter_usuario_grupo, usuario.id_usuario
							FROM inter_usuario_grupo
							INNER JOIN grupo
							ON grupo.codigo_grupo=inter_usuario_grupo.codigo_grupo
							INNER JOIN usuario
							ON usuario.id_usuario=inter_usuario_grupo.id_usuario_add 
							AND usuario.usuario='$this->author' AND contrasena='$this->gravatar' 
							AND grupo.codigo_grupo=$codigo_grupo");
		$rs=pg_fetch_array($consulta);
		$id_intermedia=$rs['id_inter_usuario_grupo'];
		$id_usuario   =$rs['id_usuario'];

		$strl=pg_query("INSERT INTO conversacion (id_inter_usuario_grupo, mensaje_texto, estatus)
			VALUES (
				$id_intermedia,
				'$this->text',
				$id_usuario) RETURNING codigo_conversacion");

		
		// Returns the MySQLi object of the DB class
		
		return pg_fetch_object($strl);
	}
}

?>