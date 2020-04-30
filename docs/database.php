<?php
namespace SQL;
const server = 'localhost';
const user   = 'root';
const pass   = 'lara';
const db     = 'ch_fsrandomizer';

class SQLConn {
	private $mysqli;
	
	public function __construct() {
		$this->mysqli = new \mysqli(server, user, pass, db);
		if($this->mysqli->connect_errno)
			trigger_error($this->mysqli->connect_error, E_USER_ERROR);
	}
	
	public function readHash($id) {
		$id    = $this->mysqli->real_escape_string($id);
		$query = $this->mysqli->query("SELECT hash FROM lists WHERE id='$id'");
		$hash  = $query->fetch_row();
		$query->free();
		
		return $hash ? $hash[0] : null;
	}
	
	public function storeHash($id, $hash) {
		$id   = $this->mysqli->real_escape_string($id);
		$hash = $this->mysqli->real_escape_string($hash);
		if(!$this->mysqli->query("INSERT INTO lists(id,hash) VALUES ('$id','$hash')"))
			trigger_error($this->mysqli->connect_error, E_USER_ERROR);
	}
}
?>