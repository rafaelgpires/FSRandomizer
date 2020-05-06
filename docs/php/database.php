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
		$hash  = $query ? $query->fetch_row() : false;
		if($query) $query->free();
		
		return $hash ? $hash[0] : null;
	}
	
	public function storeHash($id, $hash):bool {
		$id   = $this->mysqli->real_escape_string($id);
		$hash = $this->mysqli->real_escape_string($hash);
		$pass = bin2hex(openssl_random_pseudo_bytes(2));
		$name = $id;
		
		return $this->mysqli->query("INSERT INTO lists(id,hash,pass,name) VALUES ('$id','$hash','$pass','$name')");
	}
	
	public function login($id, $pass):bool {
		$id     = $this->mysqli->real_escape_string($id);
		$pass   = $this->mysqli->real_escape_string($pass);
		$query  = $this->mysqli->query("SELECT pass FROM lists WHERE id='$id'");
		$dbpass = $query ? $query->fetch_row() : false;
		if($query) $query->free();
		
		return $dbpass ? ($pass == $dbpass[0]) : false;
	}
	
	public function getName($id):string {
		$id    = $this->mysqli->real_escape_string($id);
		$query = $this->mysqli->query("SELECT name FROM lists WHERE id='$id'");
		$name  = $query ? $query->fetch_row() : false;
		if($query) $query->free();
		
		return $name ? $name[0] : '[]';
	}
}
?>