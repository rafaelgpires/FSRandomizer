<?php
namespace Lister;
const songs = 660;
const breakdownfile = "breakdown.txt";

class FSLister {
	//Options
	public $songsperchapter     = 15;		//Intended # of songs per chapter
	public $song_rng            = 25;		//Intended % of RNG within easiest songs
	public $incdiff_chance      = 1;		//Intended chance for hard encores
	public $incdiff_bonus       = 10;		//Intended % of RNG added to song_rng for hard encores
	public $superincdiff_chance = 5;		//Intended multiplier chance for much harder encores
	public $superincdiff_bonus  = 25;		//Intended % of RNG added to song_rng for much harder encores
	public $resetencores        = false;	//Whether to reset encores during each encore song
	
	//Declarations
	private $database;
	private $breakdown;
	private $songlist;
	
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
				$song           = $fssonglist[$songkey];
				$song[1]        = ($superincdiff ? '[SUPER ENCORE] ' : ($incdiff ? '[ENCORE] ' : '')) . $song[1];
				$chapters[$i][] = $song;                 //Set the new song with a prefix
				unset($fssonglist[$songkey]);            //Remove the song from the list so it doesn't repeat
				$fssonglist = array_values($fssonglist); //Reindex the array
			}
		}
		
		//Output the array
		return $chapters;
	}
}
?>
