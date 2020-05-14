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
	public function storeList($id   , $hash, $pass, $name, $desc):bool   { return $this->insert_query(array('id' => $id, 'hash' => $hash, 'pass' => $pass, 'name' => $name, 'desc' => $desc, 'visits' => 0), 'lists'); }
	public function readList ($id                               ):?array { return $this->selectID_query('`hash`, `pass`, `name`, `desc`, `visits`', 'lists', $id, false); }
	public function login    ($id   , $pass                     ):bool   { return ($pass == $this->selectID_query('pass', 'lists', $id)); }
	public function update   ($col  , $val , $id                ):bool   { return $this->mysqli->query("UPDATE lists SET `$col`='".$this->mysqli->real_escape_string($val)."' WHERE id='$id'"); }
	public function visitInc ($id   , $val                      ):bool   { return $this->mysqli->query("UPDATE lists SET `visits`=$val WHERE id='$id'"); }
	public function optRead  ($id                               ):?array { return $this->selectID_query('`fctracker`, `fchash`, `unlocker`, `speeder`, `speed`, `scorer`, `score`', 'lists', $id, false, true); }
	
	private function insert_query($options, $table) {
		foreach($options as $key=>$value)
			$options[$key] = $this->mysqli->real_escape_string($value);
		
		$optionkeys = '`' . join('`, `', array_keys($options)) . '`';
		$optionvals = '\'' . join('\', \'', $options) . '\'';
		return $this->mysqli->query("INSERT INTO $table ($optionkeys) VALUES ($optionvals)");
	}
	private function selectID_query($option, $table, $id, $first=true, $assoc=false) {
		$id     = $this->mysqli->real_escape_string($id);
		$query  = $this->mysqli->query("SELECT $option FROM $table WHERE id='$id'");
		$result = $query ? ($assoc ? $query->fetch_assoc() : $query->fetch_row()) : false;
		if($query) $query->free();
		
		return $result ? ($first ? $result[0] : $result) : null;
	}
}
?>