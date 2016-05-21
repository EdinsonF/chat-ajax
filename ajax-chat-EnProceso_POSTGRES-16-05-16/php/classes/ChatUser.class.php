<?php

class ChatUser extends ChatBase{
	
	protected $name = '', $gravatar = '';
	
	public function save(){

		$num=pg_num_rows(pg_query("SELECT name FROM webchat_users 
									WHERE name='".pg_escape_string($this->name)."'"));

		if($num>0){

			$num=pg_num_rows(pg_query("SELECT name, gravatar FROM webchat_users 
										WHERE name='".pg_escape_string($this->name)."'
										AND gravatar='".pg_escape_string($this->gravatar)."'"));

			if($num>0){
				return $num;
			}else{
				return 0;
			}
			

		}else{

			$strl=pg_query("INSERT INTO webchat_users (name, gravatar)
				VALUES (
						'".pg_escape_string($this->name)."',
						'".pg_escape_string($this->gravatar)."'
				)");
		
			return pg_affected_rows($strl);
	}

		}

		
		
	
	public function update(){
		$strl=pg_query("UPDATE webchat_users SET last_activity = NOW() WHERE name='".pg_escape_string($this->name)."' AND gravatar='".pg_escape_string($this->gravatar)."'");
				
	}
}

?>