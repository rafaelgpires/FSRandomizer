<?php
# Options
$songsperchapter     = 15;		//Intended # of songs per chapter
$song_rng            = 25;		//Intended % of RNG within easiest songs
$incdiff_chance      = 1;		//Intended chance for hard encores (1=100%, 2=50%, 4=25%, etc.)
$incdiff_bonus       = 10;		//Intended % of RNG added to song_rng for hard encores
$superincdiff_chance = 5;		//Intended multiplier chance for much harder encores (1=100%, 2=50%, 4=25%, etc.)
$superincdiff_bonus  = 45;		//Inteded % of RNG added to song_rng for much harder encores
$resetencores        = false;	//Whether to reset encores during each encore song, it'll always reset between chapters

# Includes
include('Includes\func_debug.php');			//debug($text, $type, [$title], [$inout], [$newline])
include('Includes\func_notepadTable.php');	//notepadTable($table, [$func], [$prefix])

# Get the list
$ghlist = file_get_contents('TheGHList - Phase 1.txt');
$ghlist = notepadTable($ghlist, 'table', '    • ');

# Number of songs per chapter intended
$chaptercount = ceil(count($ghlist)/$songsperchapter);
$songcount    = count($ghlist);

# Start building the chapters
$chapters = array();
for($i=0; $i<$chaptercount; $i++) {
	//Reset incdiffs
	$incdiff = false;
	$superincdiff = false;
	
	//There's not enough songs to continue, so put the remaining songs in this chapter
	if(count($ghlist) < $songsperchapter) {
		$chapters[$i] = $ghlist; break;
	}
	
	//Put songs in this chapter
	for($x=0; $x<$songsperchapter; $x++) {
		//Whether to reset encores during each song, not resetting here means that as soon as an ENCORE or SUPER ENCORE
		//shows, all the remaining encores will be at that difficulty or above. Set $resetencores to false to maintain
		//a consistent climb of difficulty among encore songs instead of randomising it on each song.
		if($resetencores) {
			//Reset incdiffs
			$incdiff = false;
			$superincdiff = false;
		}
		
		//Get a song within the first $song_rng% of available songs for 90% of the songs in the chapter
		$song = rand(0, floor(((count($ghlist)-1)/10)*($song_rng/10)));
		
		//For the final 10%, we have a chance to increase the difficulty (encores)
		if(($x+1) >= floor((($songsperchapter/10)*9))) {
			if(rand(1,$incdiff_chance) 		== $incdiff_chance)			$incdiff	  = true;
			if(rand(1,$superincdiff_chance) == $superincdiff_chance)	$superincdiff = true;
			
			if($superincdiff) {
				//It picks up a song $superincdiff_bonus further in the list
				$min = floor(((count($ghlist)-1)/10)*($superincdiff_bonus/10));
				$max = floor(((count($ghlist)-1)/10)*(($superincdiff_bonus+$song_rng)/10));
				$song = rand($min, $max);
			} else {
				if($incdiff) {
					//It picks up a song $incdiff_bonus further in the list
					$min = floor(((count($ghlist)-1)/10)*($incdiff_bonus/10));
					$max = floor(((count($ghlist)-1)/10)*(($incdiff_bonus+$song_rng)/10));
					$song = rand($min, $max);
				} //Otherwise, stays with the standard
			}
		}
		
		//Write the song into the chapter
		//Prefix to check for inc/super incs
		$ghsong = $ghlist[$song];
		$ghsong[1] = (@$superincdiff?'[SUPER ENCORE] ':(@$incdiff?'[ENCORE] ':'')) . $ghsong[1];
		$chapters[$i][] = $ghsong;			//Set the new song
		unset($ghlist[$song]);             	//Remove the song from the list so it doesn't repeat
		$ghlist = array_values($ghlist);   	//Reindex the array
	}
}

# Fix this for exporting
$count=1;
$separator = array(
		'=======',
		'===',
		'====================================================',
		'=======',
		'=======',
		'====',
		'================'
);
$export = array(
	0 => array('Chapter', '#', 'Song', '   L   ', '   R   ', 'Diff', 'Game'),
	1 => $separator
);
foreach($chapters as $chapter=>$songlist) {
	foreach($songlist as $key=>$song)
		$export[] = array(($chapter+1), ($count++), $song[1], '     ', '     ', $song[0], $song[2]);

	$export[] = $separator;
}

# Export into notepad
$export = notepadTable($export, 'notepad', '    • ');
$intro = '                                                                             ★★★★★   ★★★★★';
file_put_contents('TheGHList - Phase 2.txt', ($intro."\n".$export));
debug('Completed.');
?>