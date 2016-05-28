<?php

/* The Chat class exploses public static methods, used by ajax.php */

class Chat{
	
	public static function login($name,$email){
		if(!$name || !$email){
			throw new Exception('Llene todos los campos...');
		}

		// Preparing the gravatar hash:
		$gravatar = md5(strtolower(trim($email)));
		
		$user = new ChatUser(array(
			'name'		=> $name,
			'gravatar'	=> $gravatar
		));
		
		// The save method returns a MySQLi object
		
		if($user->save()== 0){
			throw new Exception('Usuario y/ó Contraseña Incorrecto(s)!');
		}
		
		$_SESSION['user']	= array(
			'name'		=> $name,
			'gravatar'	=> $gravatar
		);
		
		return array(
			'status'	=> 1,
			'name'		=> $name,
			'gravatar'	=> Chat::gravatarFromHash($gravatar)
		);
	}
	

	public static function checkLogged(){
		$response = array('logged' => false);
			
		if($_SESSION['user']['name']){
			$response['logged'] = true;
			$response['loggedAs'] = array(
				'name'		=> $_SESSION['user']['name'],
				'gravatar'	=> Chat::gravatarFromHash($_SESSION['user']['gravatar'])
			);
		}
		
		return $response;
	}
	

	
	public static function logout(){
		//DB::query("DELETE FROM webchat_users WHERE name = '".DB::esc($_SESSION['user']['name'])."'");
		
		$_SESSION = array();
		unset($_SESSION);

		return array('status' => 1);
	}
	
	public static function submitChat($chatText, $id_grupo){
		if(!$_SESSION['user']){
			throw new Exception('Inicia sesión para enviar mensajes');
		}
		
		if(!$chatText){
			throw new Exception('You haven\' entered a chat message.');
		}
	
		$chat = new ChatLine(array(
			'author'	=> $_SESSION['user']['name'],
			'gravatar'	=> $_SESSION['user']['gravatar'],
			'text'		=> $chatText
		));
	
		// The save method returns a MySQLi object
		$insertID = $chat->save($id_grupo);
	
		return array(
			'status'	=> 1,
			'codigo_conversacion'	=> $insertID
		);
	}
	
	public static function getUsers(){
		if($_SESSION['user']['name']){
			$user = new ChatUser(array('name' 	 => $_SESSION['user']['name'],
									   'gravatar'=> $_SESSION['user']['gravatar']));
			$user->update();
		}
		
		// Deleting chats older than 5 minutes and users inactive for 30 seconds
		
		// pg_query("DELETE FROM webchat_lines WHERE ts < SUBTIME(NOW(),'0:5:0')");
		// pg_query("DELETE FROM webchat_users WHERE last_activity < SUBTIME(NOW(),'0:0:30')");
		
		$result = pg_query('SELECT * FROM webchat_users ORDER BY name ASC LIMIT 18');
		
		$users = array();
		while($user = pg_fetch_object($result)){
			$user->gravatar = Chat::gravatarFromHash($user->gravatar,30);
			$users[] = $user;
		}
	
		return array(
			'users' => $users,
			'total' => pg_fetch_object(pg_query('SELECT COUNT(*) as total FROM webchat_users')));
	}
	

	public static function getChats($lastID, $id_grupo){
		$chats = array();

		if(!$_SESSION['user']['name']){
			
			return array('chats' => -1);
		}else{


		$result = pg_query('SELECT conversacion.codigo_conversacion, inter_usuario_grupo.id_inter_usuario_grupo, nombre_grupo, usuario.usuario, usuario.contrasena, usuario.id_usuario, mensaje_texto, fecha_envio FROM inter_usuario_grupo
							INNER JOIN grupo
							ON grupo.codigo_grupo=inter_usuario_grupo.codigo_grupo
							INNER JOIN usuario
							ON usuario.id_usuario=inter_usuario_grupo.id_usuario_add 
							INNER JOIN conversacion
							ON conversacion.id_inter_usuario_grupo=inter_usuario_grupo.id_inter_usuario_grupo
							AND grupo.codigo_grupo = '.$id_grupo.' AND  conversacion.codigo_conversacion > '.$lastID.' ORDER BY codigo_conversacion ASC');
		
		
		while($chat = pg_fetch_array($result)){
			
			// Returning the GMT (UTC) time of the chat creation:
			$chat['time'] = array(
				'hours'		=> gmdate('H',strtotime($chat['fecha_envio'])),
				'minutes'	=> gmdate('i',strtotime($chat['fecha_envio']))
			);
			
			$chat['gravatar'] = Chat::gravatarFromHash($chat['gravatar']);

			$chats[] = $chat;
			
		}
	
		return array('chats' => $chats);
		}
	}
	
	public static function gravatarFromHash($hash, $size=23){
		return 'http://www.gravatar.com/avatar/'.$hash.'?size='.$size.'&amp;default='.
				urlencode('http://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?size='.$size);
	}



	//--------MI AGREGADO---->>>

	public static function CrearGroup_AddUsser($usserAdd, $nombreGroup){

		if(!$usserAdd || !$nombreGroup){

			throw new Exception('Llene todos los campos (Usuario - Grupo)...');
		}

		$usser= new ChatUser (array(
				'name'		=> $_SESSION['user']['name'],
				'gravatar'	=> $_SESSION['user']['gravatar']
			));
		$patrocinador=$usser->id_usuarioActivo();

		$grupo=new ChatGrupo(array(
			'usuario'		=> $usserAdd,
			'grupo'			=> $nombreGroup,
			'patrocinador'	=> $patrocinador
		));

		$resultado=$grupo->CrearGrupo_AddUsuario();

	    return array('result' => $resultado);



	}


	public static function CargarMisGrupos(){

		

		$usser= new ChatUser (array(
				'name'		=> $_SESSION['user']['name'],
				'gravatar'	=> $_SESSION['user']['gravatar']
			));
		$patrocinador=$usser->id_usuarioActivo();


		$grupo=new ChatGrupo(array(
			'patrocinador'	=> $patrocinador
		));


		$resultado=$grupo->CargarMisGruposBD();

	    return array('Grupos' => $resultado);



	}



}


?>