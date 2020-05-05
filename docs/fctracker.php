<?php
/* Page: FCTracker
 *
 * Description:		This page shows the $list->fslist and allows the user to interact with it.
 * Dependencies:	$this->fslist should be set.
 */
 if(!$list->readHash()) error('FCTracker was loaded without a valid list.', true);
?>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/fsrandomizer.css">
	<title>FSRandomizer - FC Tracker</title>
</head>
<body>
	<!-- Content -->
	<div class="container vh-100">
		<!-- Navbar -->
		<div class="row py-4 align-items-top">
			<?=$html->navbar?>
		</div>
		
		<!-- FC Tracker -->
		<div class="row align-items-center">
			<div class="container">
				<!-- Header -->
				<div class="row d-flex justify-content-center listheader">
					<div class="container">
						<div class="row listicon">
							<img src="./images/diff_10.png" />
						</div>
						<div class="row listtitle">
							<h1><?=$list->listID?></h1>
							
						</div>
						<div class="row listdescription">
							<h2>Full Series List</h2>
						</div>
						<div class="row listfiller">&nbsp;</div>
					</div>
				</div>
				
				<!-- Alerts -->
				<div class="row mx-auto" style="max-width: 932">
					<table class="table m-0">
						<!-- Alerts -->
						<thead><th colspan=4>Your password is: <span class="pass">AlGh123123</span> - Save it, this is the last time you will see it.</th></thead>
						<thead><th colspan=4>Your FC Tracker is disabled. Click <a href="#">here</a> to enable it.</th></thead>
						<thead class="text-center"><th>Stats</th><th>20 / 660 FCs</th><th>235% Avg. Speed</th><th>14,304,432 Acc. Score</th></thead>
						<thead class="text-center"><th>Stats</th><th class="Diff10">660 / 660 FCs</th><th>Speed <a href="#">disabled</a></th><th>Score <a href="#">disabled</a></th></thead>
					</table>
				</div>
				
				<!-- List -->
				<div class="row mx-auto" style="max-width: 932">
					<table class="table table-borderless">
						<tr><td colspan=6 class="chapter">Chapter 1</td></tr>
						<tr>
							<td>Song name</td>
							<td>FC</td>
							<td>Speed</td>
							<td>Score</td>
							<td>Difficulty</td>
							<td>Game</td>
						</tr>
						<tr>
							<td>Song name</td>
							<td>FC</td>
							<td>Speed</td>
							<td>Score</td>
							<td>Difficulty</td>
							<td>Game</td>
						</tr>
						<tr>
							<td>Song name</td>
							<td>FC</td>
							<td>Speed</td>
							<td>Score</td>
							<td>Difficulty</td>
							<td>Game</td>
						</tr>
						<tr><td colspan=6>Chapter1</td></tr>
						<tr>
							<td>Song name</td>
							<td>FC</td>
							<td>Speed</td>
							<td>Score</td>
							<td>Difficulty</td>
							<td>Game</td>
						</tr>
						<tr>
							<td>Song name</td>
							<td>FC</td>
							<td>Speed</td>
							<td>Score</td>
							<td>Difficulty</td>
							<td>Game</td>
						</tr>
						<tr>
							<td>Song name</td>
							<td>FC</td>
							<td>Speed</td>
							<td>Score</td>
							<td>Difficulty</td>
							<td>Game</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</body>
</html>