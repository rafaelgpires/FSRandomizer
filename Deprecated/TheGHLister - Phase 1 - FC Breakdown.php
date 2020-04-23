<?php
# Includes
include('Includes\func_debug.php');			//debug($text, $type, [$title], [$inout], [$newline])
include('Includes\func_notepadTable.php');	//notepadTable($table, [$func], [$prefix])

# Get the list
$ghlist = file_get_contents('THEGHList - Phase 0 - SHFC Breakdowns.txt');

# Break it down into an array
$ghgames = array(); $count = 0;
foreach(explode("\n", $ghlist) as $line) {
	if(empty(trim($line))) { $count++; continue; }
	$ghgames[$count][] = trim($line);
}

# Name the games
$ghgames = array(
	'Guitar Hero 1   ' => $ghgames[0],
	'Guitar Hero 2   ' => $ghgames[1],
	'Guitar Hero: 80s' => $ghgames[2],
	'Guitar Hero 3   ' => $ghgames[3],
	'Guitar Hero: A  ' => $ghgames[4],
	'Guitar Hero: WT ' => $ghgames[5],
	'Guitar Hero: M  ' => $ghgames[6],
	'Guitar Hero: SH ' => $ghgames[7],
	'Guitar Hero: VH ' => $ghgames[8],
	'Guitar Hero 5   ' => $ghgames[9],
	'Guitar Hero 6   ' => $ghgames[10]
);

# Map the array by songs, add % rank relative to their position in the breakdown
$ghsongs = array();
foreach($ghgames as $game=>$songlist)
	foreach($songlist as $rank=>$song)
		$ghsongs[] = array($song, $game, ((($rank+1)/count($songlist))*100));
$songcount = count($ghsongs);

# Define ranking for each game
$stdranking = array( //Standard ranking format
	//% represents place in the breakdown list relative to the total of songs in it
	1 => array(0, 19),
	2 => array(20, 29),
	3 => array(30, 39),
	4 => array(40, 49),
	5 => array(50, 59),
	6 => array(60, 69),
	7 => array(70, 79),
	8 => array(80, 89),
	9 => array(90, 95),
	10 => array(96, 100)
);

$ranking = array( //Ranking format for each game
	'Guitar Hero 1   ' => array(
		1 => array( 0, 21),
		2 => array(22, 33),
		3 => array(34, 39),
		4 => array(40, 60),
		5 => array(61, 70),
		6 => array(71, 82),
		7 => array(83, 92),
		8 => array(93, 100)
	),
	'Guitar Hero 2   ' => array(
		1 => array( 0,  2),
		2 => array( 3, 20),
		3 => array(21, 31),
		4 => array(32, 50),
		5 => array(51, 66),
		6 => array(67, 75),
		7 => array(76, 83),
		8 => array(84, 92),
		9 => array(93, 99),
		10 => array(100, 100)
	),
	'Guitar Hero: 80s' => array(
		1 => array( 0, 12),
		2 => array(13, 32),
		3 => array(33, 42),
		4 => array(43, 52),
		5 => array(53, 65),
		6 => array(66, 72),
		7 => array(73, 82),
		8 => array(83, 89),
		9 => array(90, 95),
		10 => array(96, 100)
	),
	'Guitar Hero 3   ' => array(
		1 => array( 0, 17),
		2 => array(18, 29),
		3 => array(30, 40),
		4 => array(41, 50),
		5 => array(51, 60),
		6 => array(61, 70),
		7 => array(71, 81),
		8 => array(82, 91),
		9 => array(92, 96),
		10 => array(97, 100)
	),
	'Guitar Hero: A  ' => array(
		1 => array( 0, 11),
		2 => array(12, 33),
		3 => array(34, 52),
		4 => array(53, 67),
		5 => array(68, 79),
		6 => array(80, 89),
		7 => array(90, 96),
		8 => array(97, 100)
	),
	'Guitar Hero: WT ' => array(
		1 => array( 0, 14),
		2 => array(15, 29),
		3 => array(30, 45),
		4 => array(46, 59),
		5 => array(60, 65),
		6 => array(66, 72),
		7 => array(73, 82),
		8 => array(83, 90),
		9 => array(91, 99),
		10 => array(100, 100)
	),
	'Guitar Hero: M  ' => array(
		1 => array( 0,  9),
		2 => array(10, 15),
		3 => array(16, 21),
		4 => array(22, 33),
		5 => array(34, 41),
		6 => array(42, 45),
		7 => array(46, 70),
		8 => array(71, 78),
		9 => array(79, 96),
		10 => array(97, 100)
	),
	'Guitar Hero: SH ' => array(
		1 => array( 0, 13),
		2 => array(14, 24),
		3 => array(25, 44),
		4 => array(45, 51),
		5 => array(52, 59),
		6 => array(60, 71),
		7 => array(72, 80),
		8 => array(81, 90),
		9 => array(91, 96),
		10 => array(97, 100)
	),
	'Guitar Hero: VH ' => array(
		1 => array( 0, 18),
		2 => array(19, 28),
		3 => array(29, 41),
		4 => array(42, 47),
		5 => array(48, 60),
		6 => array(61, 69),
		7 => array(70, 79),
		8 => array(80, 92),
		9 => array(93, 99),
		10 => array(100, 100)
	),
	'Guitar Hero 5   ' => array(
		1 => array( 0, 20),
		2 => array(21, 29),
		3 => array(30, 40),
		4 => array(41, 49),
		5 => array(50, 59),
		6 => array(60, 69),
		7 => array(70, 80),
		8 => array(81, 94),
		9 => array(95, 100)
	),
	'Guitar Hero 6   ' => array(
		1 => array( 0, 19),
		2 => array(20, 30),
		3 => array(31, 39),
		4 => array(40, 49),
		5 => array(50, 59),
		6 => array(60, 68),
		7 => array(69, 79),
		8 => array(80, 88),
		9 => array(89, 96),
		10 => array(97, 100)
	)
);

# Rank them
array_walk($ghsongs, function(&$song, $bdrank) use ($ranking, $songcount) {
	foreach($ranking[$song[1]] as $keyrank=>$numrank)
		if(floor($song[2]) >= $numrank[0] && floor($song[2]) <= $numrank[1])
			$rank = $keyrank; //Custom ranking

	$song = array($song[2], $rank, $song[0], $song[1]);
}); $ghsongs[0][0] = 1;
		
# Sort them by their new rank
array_multisort(
	array_column($ghsongs, 1), SORT_NUMERIC, SORT_ASC,
	array_column($ghsongs, 0), SORT_NUMERIC, SORT_ASC,
	$ghsongs
);

# Export into notepad
foreach($ghsongs as &$song) { $song = array_slice($song, 1); } //Remove perc
$ghsongs = notepadTable($ghsongs, 'notepad', '    • ');
file_put_contents('TheGHList - Phase 1 - Master FC Breakdown.txt', $ghsongs);
debug('Completed.');
?>