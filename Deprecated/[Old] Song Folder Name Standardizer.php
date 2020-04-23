<?php
	//No longer needed as the standardized folder is now in "Original Series + DLC.7z"

	$ghfolders = array();
	exec("dir", $output);
	for($i=0;$i<7;$i++) array_shift($output);
	for($i=0;$i<4;$i++) array_pop($output);
	foreach($output as $line) {
		$line = explode("<DIR>", $line);
		$ghfolders[] = trim($line[1]);
	}

	foreach($ghfolders as $ghgame) {
		$output = array();
		exec("dir \"$ghgame\"", $output);
		for($i=0;$i<7;$i++) array_shift($output);
		for($i=0;$i<2;$i++) array_pop($output);
		
		$ghsongs = array();
		foreach($output as $line) {
			$line = explode("<DIR>", $line);
			$ghsongs[] = trim($line[1]);
		}
		
		foreach($ghsongs as $ghsong) {
			$songinfo = file_get_contents("$ghgame/$ghsong/song.ini");
			$songinfo = explode("\n", $songinfo);
			$artist = '';
			$name = '';
			foreach($songinfo as $line) {
				if(preg_match('/^artist( )?=( )?(.+)$/', trim($line), $matches)) $artist = $matches[3];
				if(preg_match('/^name( )?=( )?(.+)$/', trim($line), $matches)) $name = $matches[3];
			}
			if(!$artist) die("Error in game $ghgame in song $ghsong. No artist found.");
			if(!$name) die("Error in game $ghgame in song $ghsong. No name found.");
			$song = "$artist - $name";
			$song = preg_replace("/(\?)|(\/)|(:)|(\")/", "", $song);
			$song = preg_replace("/\.$/", "", $song);
			rename("$ghgame/$ghsong", "$ghgame/$song") or 
				die("Error in game $ghgame in song $ghsong. Couldn't rename to $song");
			echo "$ghgame > $song renamed.\n";
		}
	}
?>