<?php

/* Chat line is used for the chat entries */

class ChatLine extends ChatBase{
	
	protected $text = '', $author = '', $gravatar = '';
	
	public function save(){
		$strl=pg_query("
			INSERT INTO webchat_lines (author, gravatar, text)
			VALUES (
				'".pg_escape_string($this->author)."',
				'".pg_escape_string($this->gravatar)."',
				'".pg_escape_string($this->text)."') RETURNING id");

		
		// Returns the MySQLi object of the DB class
		
		return pg_fetch_object($strl);
	}
}

?>