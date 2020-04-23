<?php
	//Run this inside the Clone Hero songs folder with a standardized song directory
	include("func_notepadTable.php");
	
	function recurse_copy($src,$dst) { 
		$dir = opendir($src); 
		@mkdir($dst); 
		while(false !== ( $file = readdir($dir)) ) { 
			if (( $file != '.' ) && ( $file != '..' )) { 
				if ( is_dir($src . '/' . $file) ) { 
					recurse_copy($src . '/' . $file,$dst . '/' . $file); 
				} 
				else { 
					copy($src . '/' . $file,$dst . '/' . $file); 
				} 
			} 
		} 
		closedir($dir); 
	} 
	
	//Get the table
	$list = file_get_contents("TheGHList.txt");
	$list = notepadTable($list, 'table', '    • ');
	array_shift($list); //Remove header
	
	//Template for matching the names to the folder
	$gamenames = array(
		"Guitar Hero: A" => "Guitar Hero - Aerosmith",
		"Guitar Hero: M" => "Guitar Hero - Metallica",
		"Guitar Hero: SH" => "Guitar Hero - Smash Hits",
		"Guitar Hero: VH" => "Guitar Hero - Van Halen",
		"Guitar Hero 6" => "Guitar Hero - Warriors of Rock",
		"Guitar Hero 5" => "Guitar Hero 5",
		"Guitar Hero: 80s" => "Guitar Hero Encore Rock The 80s",
		"Guitar Hero 1" => "Guitar Hero I",
		"Guitar Hero 2" => "Guitar Hero II",
		"Guitar Hero 3" => "Guitar Hero III",
		"Guitar Hero: WT" => "Guitar Hero World Tour"
	);
	
	//Get an array with the folder song names
	$songdir = array();
	foreach($gamenames as $gamefolder) {
		//Get all the songs from this game
		$songs = scandir($gamefolder);
		
		//Get rid of the directory commands
		array_shift($songs);
		array_shift($songs);
		
		//Filter through each song
		foreach($songs as $key=>$song) {
			//Ignore career/co-op variants
			if(preg_match("/(Career)|(Co-op)$/", trim($song))) continue;
			
			$dir = $song; //Save directory name
			$song = preg_replace("/^.+? - /", "", $song); //Remove artist name
			
			//Register the song
			$songdir[$gamefolder][] = array($dir, $song);
		}
	}
	
	//Parse the notepad array
	$errors = array();
	foreach($list as $listsong) {
		//Ignore separators
		if($listsong[1] == "===") continue;
		
		//Check the game's name
		if(!isset($gamenames[$listsong[6]])) die("Game name not accounted for in song ".$listsong[1].".");
		
		//Zero-fill chapter and song numbers
		if($listsong[0] < 10) $listsong[0] = "0" . $listsong[0];
		if($listsong[1] < 100) {
			if($listsong[1] < 10) $listsong[1] = "00" . $listsong[1];
			else $listsong[1] = "0" . $listsong[1];
		}
		
		//Remove encore prefixes from song names but add them in the new dir
		$encore = "";
		if(preg_match("/(\[ENCORE\] |\[SUPER ENCORE\] )/", $listsong[2], $matches)) {
			$listsong[2] = preg_replace("/\[ENCORE\] |\[SUPER ENCORE\] /", "", trim($listsong[2]));
			$encore = $matches[1];
		}
		
		//Parse the array into named variables
		$chapter  = $listsong[0];
		$songnum  = $listsong[1];
		$songname = $listsong[2];
		$songdiff = $listsong[5];
		$gamename = $gamenames[$listsong[6]];
		
		//Create chapter folder if it doesn't already exist
		if(!is_dir("Chapter $chapter")) mkdir("Chapter $chapter") or
			die("Failed to create dir for chapter $chapter.");
		
		//Find song in $songdir array
		if(!isset($songdir[$gamename])) die("Internal error with the game name: $gamename.");
		$found = false;
		foreach($songdir[$gamename] as $song) {
			if(strtolower($song[1]) == strtolower($songname)) {
				$found = true;
				$songname = $encore . $songname;
				recurse_copy(($gamename."/".$song[0]."/"),("Chapter $chapter/[$songnum] [$songdiff] $songname"));
			}
		} if(!$found) {
			$error = "Couldn't find song $songname from $gamename in \$songdir.\n";
			echo($error); $errors[] = $error;
		}
		
		//Output
		echo "Song $songnum copied.\n";
	}
	
	//Output
	echo "Completed with ".count($errors)." error(s).\n";
	if(!empty($errors))
		foreach($errors as $key=>$error)
			echo ($key+1) . " => $error";
?>