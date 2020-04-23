<?php
namespace SQL;
const server = 'localhost';
const user   = 'root';
const pass   = 'lara';
const db     = 'ch_fsrandomizer';

class SQLConn {
	private $mysqli;
	public function __construct() {
		$mysqli = new \mysqli(server, user, pass, db);
		if($mysqli->connect_errno) die("Failed to connect to MySQL: " . $mysqli->connect_errno . ") " . $mysqli->connect_error);
	}
	
	public function readHash($id) {
		$id    = $mysqli->real_escape_string($id);
		$query = $mysqli->query("SELECT hash FROM lists WHERE id='$id'");
		$hash  = $query->fetch_row();
		$query->free();
		
		return $hash[0];
	}
	
	public function storeHash($id, $hash) {
		$id   = $mysqli->real_escape_string($id);
		$hash = $mysqli->real_escape_string($hash);
		if(!$mysqli->query("INSERT INTO lists(id,hash) VALUES ('$id','$hash')"))
			die("Failed to store the list: " . $mysqli_errno . ") " . $mysqli->error);
	}
}
?>