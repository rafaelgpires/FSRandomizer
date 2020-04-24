<?php
namespace Lister;
const songs = 660;
const breakdownfile = "breakdown.txt";

class FSLister {
	#Properties
	public $songsperchapter     = 15;		//Intended # of songs per chapter
	public $song_rng            = 25;		//Intended % of RNG within easiest songs
	public $incdiff_chance      = 1;		//Intended chance for hard encores
	public $incdiff_bonus       = 10;		//Intended % of RNG added to song_rng for hard encores
	public $superincdiff_chance = 5;		//Intended multiplier chance for much harder encores
	public $superincdiff_bonus  = 25;		//Intended % of RNG added to song_rng for much harder encores
	public $resetencores        = false;		//Whether to reset encores during each encore song

	private $database;				//Instance of /SQL/SQLConn
	private $breakdown;				//For reading breakdown.txt
	private $songlist;				//For interpreting breakdown.txt

	public $fslist;					//Output: Array list
	public $hash;					//Output: Hash of the list
	public $listID;					//Unique ID given to the created list
	
	#Methods
	public function __construct() {
		$this->database  = new \SQL\SQLConn();
		$this->breakdown = file_get_contents(breakdownfile);	
		$this->songlist  = \Includes\notepadTable($this->breakdown, 'table', '    • ');
	}
	public function createList() {
		//Count the chapters
		$chaptercount = ceil(songs/$this->songsperchapter);
		$fssonglist   = $this->songlist;
		$chapters     = array();
		
		//Start looping through the chapters
		for($i=0; $i<$chaptercount; $i++) {
			//Reset encores every chapter
			$incdiff      = false;
			$superincdiff = false;
			
			//There's not enough songs to fill the chapter, so dump the remaining songs here
			if(count($fssonglist) < $this->songsperchapter) {
				$chapters[$i] = $fssonglist;
				break;
			}
			
			//Put songs in this chapter
			for($x=0; $x<$this->songsperchapter; $x++) {
				//Reset encores during each song if the option is enabled
				if($this->resetencores) {
					$incdiff      = false;
					$superincdiff = false;
				}
				
				//Final 10% of songs have a chance for encores
				if(($x+1) >= floor((($this->songsperchapter/10)*9))) {
					if(rand(1, $this->incdiff_chance)      == 1) $incdiff      = true;
					if(rand(1, $this->superincdiff_chance) == 1) $superincdiff = true;
					$diffbonus = $superincdiff ? $this->superincdiff_bonus : ($incdiff ? $this->incdiff_bonus : 0);
				} else $diffbonus = 0;
				
				//Get a song within 10%*$song_rng + $diffbonus of available songs
				$min     = $diffbonus ? floor(((count($fssonglist)-1)/10)*($diffbonus/10)) : 0;
				$max     = floor(((count($fssonglist)-1)/10)*(($diffbonus+$this->song_rng)/10));
				$songkey = rand($min, $max);
				
				//Write the song into the chapter
				$song    = $fssonglist[$songkey];
				$song[3] = $this->findSongKey($song);
				$song[1] = ($superincdiff ? '[SUPER ENCORE] ' : ($incdiff ? '[ENCORE] ' : '')) . $song[1];
				$chapters[$i][] = $song;		//Set the new song with a prefix and store the key
				unset($fssonglist[$songkey]);		//Remove the song so it doesn't repeat
				$fssonglist = array_values($fssonglist);//Reindex the array
			}
		}
		
		//Hash and store the list
		$this->fslist = $chapters;
		$this->createHash();
		$this->storeList();
	}
	public function getList($id) {
		$this->hash = $this->database->readHash($id);
		if($this->hash) $this->readHash();
		else trigger_error("Invalid ID.", E_USER_ERROR);
	}

	private function createHash() {
		$this->hash = ''; //Reset hash

		foreach($this->fslist as $chapter) {
			$this->hash .= '|'; //Write chapter separator
			foreach($chapter as $song) {
				//Check for encores
				preg_match('/^(\[ENCORE\] )|(\[SUPER ENCORE\] )/', $song[1], $encore);
				switch(true) {
					case isset($encore[1]): $encore = 1; break;
					case isset($encore[2]): $encore = 2; break;
					default: $encore = 0;
				}
				
				//Write the hash for the song
				$this->hash .= $encore . str_pad($song[3], 3, 0, STR_PAD_LEFT);
			}
		}
		
		//Store hash
		$this->hash = substr($this->hash, 1);
	}
	private function readHash($hash = null) {
		$hash = $hash ? $hash : $this->hash;
		
		//Check for errors
		if(!$hash) trigger_error("No hash given.", E_USER_ERROR);
		
		//Reset the list
		$this->hash   = $hash;
		$this->fslist = array();
		
		//Read the hash by chapters
		$hashlist = explode('|', $hash);
		foreach($hashlist as $hashchapter=>$hashsongs) {
			for($i=0; $i<strlen($hashsongs); ($i = $i+4)) {
				//Encore switch
				switch($hashsongs[($i)]) {
					case 1: $prefix = '[ENCORE] '; break;
					case 2: $prefix = '[SUPER ENCORE] '; break;
					default: $prefix = '';
				}
				
				//Get the song
				$songid  = (int)substr($hashsongs, ($i+1), 3);
				$song    = $this->songlist[$songid];
				$song[1] = $prefix . $song[1];
				$song[3] = $songid;
				
				//Register the song in the chapter
				$this->fslist[$hashchapter][] = $song;
			}
		}
	}
	private function storeList() {
		$this->listID = uniqid(); //Create a unique ID for the list
		$this->database->storeHash($this->listID, $this->hash);
	}
	private function findSongKey($songarr) {
		foreach($this->songlist as $key=>$song) {
			//Match song name and game
			if($song[1] == $songarr[1] && $song[2] == $songarr[2])
				return $key;
		}
		
		//Song not found
		trigger_error("Song not found?", E_USER_ERROR);
	}
}
?>