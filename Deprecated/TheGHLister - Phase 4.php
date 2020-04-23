<?php
	//Run this in the songs directory with only the Chapter folders, Customs and DLC
	//Get all the chapters
	$chapters = scandir("./");
	array_shift($chapters); //Get rid of .
	array_shift($chapters); //Get rid of ..
	array_pop($chapters); //Get rid of this script
	array_pop($chapters); //Get rid of Customs
	array_pop($chapters); //Get rid of DLC
	
	//Parse all the chapters
	foreach($chapters as $chapterdir) {
		//Get the chapter #
		preg_match('/Chapter ([0-9]+)/', trim($chapterdir), $matches);
		$chapter = $matches[1];
		if(!is_numeric($chapter)) die("Couldn't process chapter $chapterdir.");
		
		//Get the songs for the chapter
		$songs = scandir($chapterdir);
		array_shift($songs); //Get rid of .
		array_shift($songs); //Get rid of ..
		
		//Parse all the songs
		$chsong = 0;
		foreach($songs as $song) {
			preg_match('/^\[([0-9]+)\] \[([0-9]+)\] (.+$)/', trim($song), $matches);
			$chsong++;
			$songnum 	= $matches[1];
			$songdiff 	= $matches[2];
			$songname 	= $matches[3];
			
			//Check for errors
			if((!is_numeric($songnum)) || (!is_numeric($songdiff)) || (!$songname))
				die("Couldn't process song $song from $chapterdir.");
			
			//Find the ini
			$songini = file_get_contents("$chapterdir/$song/song.ini")
				or die("Couldn't find song.ini for song $song in $chapterdir.");
				
			//Parse the ini
			$songini = explode("\n", $songini);
			$found = array(false, false, false);
			foreach($songini as $line=>$ini) {
				if(preg_match("/^([a-z0-9_]+) ?= ?(.+)$/", trim($ini), $matches)) {
					switch($matches[1]) {
						case "name": $songini[$line] = "name = [$songnum] $songname"; $found[0] = true; break;
						case "diff_guitar": $songini[$line] = "diff_guitar = $songdiff"; $found[1] = true; break;
						case "playlist_track": $songini[$line] = "playlist_track = $chsong"; $found[2] = true; break;
						default:
							//Remove other difficulty ratings
							if(substr($matches[1], 0, 5) == "diff_") unset($songini[$line]);
					}
				}
			}
			
			//Check for errors
			if(!$found[0] || !$found[1]) die("Couldn't process ini for song $song in $chapterdir.");
			if(!$found[2]) $songini[] = "playlist_track = $chsong";
			
			//Write new ini
			file_put_contents("$chapterdir/$song/song.ini", implode("\n", $songini)) or
				die("Couldn't write ini for song $song in $chapterdir.");
				
			//Output
			echo "Song $songnum's ini editted.\n";
		}
	}
	
	//Output
	echo "Completed.";
?>