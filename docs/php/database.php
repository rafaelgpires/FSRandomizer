<?php
const server = 'localhost';
const user   = 'root';
const pass   = 'lara';
const db     = 'ch_fsrandomizer';

class SQLConn {
	private $mysqli;
	
	public function __construct() {
		$this->mysqli = new \mysqli(server, user, pass, db);
		if($this->mysqli->connect_errno)
			error("Couldn't connect to database.", true);
	}
	
	public function readHash($id):?string {
		$id    = $this->mysqli->real_escape_string($id);
		$query = $this->mysqli->query("SELECT hash FROM lists WHERE id='$id'");
		$hash  = $query->fetch_row();
		$query->free();
		
		return $hash ? $hash[0] : null;
	}
	
	public function storeHash($id, $hash):bool {
		$id   = $this->mysqli->real_escape_string($id);
		$hash = $this->mysqli->real_escape_string($hash);
		return $this->mysqli->query("INSERT INTO lists(id,hash) VALUES ('$id','$hash')");
	}
}
?>