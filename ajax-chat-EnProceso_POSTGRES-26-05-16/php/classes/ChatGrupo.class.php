<?php
class ChatGrupo extends ChatBase{

	protected $usuario = '', $grupo = '', $patrocinador='';


	function CrearGrupo_AddUsuario(){
	//$list=array();
	$retorno=0;
	
	$consulto=pg_query("SELECT codigo_grupo FROM grupo WHERE nombre_grupo='$this->grupo'");
	$num=pg_num_rows($consulto);
	if($num>0){
			$reg=pg_fetch_array($consulto);
			$codigo_grupo=$reg['codigo_grupo'];

			$Inter=pg_query("SELECT codigo_grupo, id_usuario_add FROM inter_usuario_grupo WHERE codigo_grupo='$codigo_grupo' AND id_usuario_add='$this->patrocinador'");
			$num=pg_num_rows($Inter);

			if($num==1){

				return $this->addUsuarios_Grupo($codigo_grupo);
				


			}else{
				//---CREADOR DEL GRUPO
				$inserto=pg_query("INSERT INTO inter_usuario_grupo (codigo_grupo,id_usuario_add,usuario_patrocinador, estatus)
						VALUES('$codigo_grupo','$this->patrocinador','$this->patrocinador','CREADOR')") or die('ERROR AL INSERTAR DATOS: ' . pg_last_error());
				$registro = pg_affected_rows($inserto);
				if($registro>0){

					return $this->addUsuarios_Grupo($codigo_grupo);

				}else{
					
					return $retorno=0;//----ERROR
					
				}
			}


	}else{
		$insert=pg_query("INSERT INTO grupo (nombre_grupo,estatus)
							VALUES('$this->grupo', 'ACTIVO')")or die('ERROR AL INSERTAR DATOS: ' . pg_last_error());

		$registro = pg_affected_rows($insert);

		if($registro>0){

				$consulto2=pg_query("SELECT codigo_grupo FROM grupo WHERE nombre_grupo='$this->grupo'");
				$reg=pg_fetch_array($consulto2);
				$codigo_grupo=$reg['codigo_grupo'];

					//---CREADOR DEL GRUPO
					$inserto=pg_query("INSERT INTO inter_usuario_grupo (codigo_grupo,id_usuario_add,usuario_patrocinador, estatus)
							VALUES('$codigo_grupo','$this->patrocinador','$this->patrocinador','CREADOR')") or die('ERROR AL INSERTAR DATOS: ' . pg_last_error());
					$registro = pg_affected_rows($inserto);
					if($registro>0){

						return $this->addUsuarios_Grupo($codigo_grupo);

					}else{
						
						return $retorno=0;//----ERROR
						
					}
				



		}else{
			
			return $retorno=0;//---ERROR
			
		}
	}

	

}



function addUsuarios_Grupo($codigo_grupo){

	$retorno=0;


	$consultoUsser=pg_query("SELECT id_usuario FROM usuario WHERE usuario='$this->usuario'");
	$num=pg_num_rows($consultoUsser);
	$reg =pg_fetch_array($consultoUsser);
	$id_usuarioAdd=$reg['id_usuario'];


	if($num>0){

			$Inter=pg_query("SELECT codigo_grupo, id_usuario_add FROM inter_usuario_grupo WHERE codigo_grupo='$codigo_grupo' AND id_usuario_add='$id_usuarioAdd'");
			$num=pg_num_rows($Inter);

			if($num==1){
				
				
				return $retorno=2;//----YA ESTAS AGREGADO A ESTE GRUPO
				

			}else{


			$inserto=pg_query("INSERT INTO inter_usuario_grupo (codigo_grupo,id_usuario_add,usuario_patrocinador, estatus)
					VALUES('$codigo_grupo','$id_usuarioAdd','$this->patrocinador','ACTIVO')") or die('ERROR AL INSERTAR DATOS: ' . pg_last_error());
			$registro = pg_affected_rows($inserto);
			if($registro>0){

				
				return $retorno=1;//---GRUPO CREADO Y USUARIO AGREGADO
				

			}else{
				return $retorno; //---ERROR
			}


			}


	}else{
		 
		 return $retorno=3;//---EL USURIO NO EXISTE
		 
	}

	


}


function CargarMisGruposBD(){
	
	$strl=pg_query("SELECT grupo.codigo_grupo, grupo.nombre_grupo FROM inter_usuario_grupo 
					INNER JOIN grupo 
					ON grupo.codigo_grupo=inter_usuario_grupo.codigo_grupo
					AND  id_usuario_add='$this->patrocinador'");

	while($reg=pg_fetch_assoc($strl)){
			$list[]=$reg;

		}

		return $list;					
} 

}//----FIN DE LA CLASE

?>