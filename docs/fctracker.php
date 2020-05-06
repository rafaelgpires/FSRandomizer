<?php
/* Page: FCTracker
 *
 * Description:		This page shows the $list->fslist and allows the user to interact with it.
 * Dependencies:	$this->fslist should be set.
 */
//Check for dependencies
if(!$list->readHash()) error('FCTracker was loaded without a valid list.', true);

//List functions
function tableChapter($chapter) { echo "<tr><td colspan=3 class=\"chapter\">$chapter</td></tr>\n"; }
function tableSong($songArr) {
	//Check for encores
	$song = preg_replace_callback("/^(\[(ENCORE)\] |\[(SUPER ENCORE)\] )/", function($encore){
		global $songArr;
		if(isset($encore[3])) {
			//Super Encore, check for difficulty icon
			if($songArr[0] >= 5) return '<img src="./images/diff_'.$songArr[0].'.png" class="diffIcon" /><b>Super Encore</b>: ';
			else return '<b>Super Encore</b>: ';
		} else return '<b>Encore</b>: '; //Encore
	}, $songArr[1]); //No encore
	
	//Add difficulty colors
	$diff = $songArr[0];
	$diff = "<span class=\"Diff$diff\">$diff / 10</span>";
	
	//Set game
	$game = $songArr[2];
	echo <<<EOL
<tr>
	<td>$song</td>
	<td>$diff</td>
	<td>$game</td>
</tr>
EOL;
}
?>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<?=$html->styles?>
	<title>FSRandomizer - FC Tracker</title>
	<script>var ListID="<?=$list->listID?>";</script>
	<script>var logged=<?=json_encode($logged)?>;</script>
</head>
<body>
	<!-- Password Modal -->
	<div class="modal fade" tabindex="-1" id="modalpass">
		<div class="modal-dialog modal-dialog-centered modal-sm modalpass">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">List Password</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form class="form-inline">
						<input type="text" class="form-control passinput" placeholder="Password" id="passinput" />
					</form>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" id="submitpass">Submit</button>
				</div>
			</div>
		</div>
	</div>

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
							<h1 id="listname" data-show="#inputName"><?=$list->listName?></h1>
							<input ="text" class="listname" value="<?=$list->listName?>" id="inputName" data-show="#listname" data-name="name" maxlength=13 />
						</div>
						<div class="row listdescription">
							<h2 id="listdesc" data-show="#inputDesc"><?=$list->listDesc?></h2>
							<input ="text" class="listdesc" value="<?=$list->listDesc?>" id="inputDesc" data-show="#listdesc" data-name="desc" maxlength=45 />
						</div>
						<div class="row listfiller">&nbsp;</div>
					</div>
				</div>
				
				<!-- Alerts -->
				<div class="row mx-auto" style="max-width: 932">
					<table class="table m-0">
						<thead><th colspan=4>Your password is: <span class="pass">AlGh123123</span> - Save it, this is the last time you'll see it.</th></thead>
						<thead><th colspan=4>Your FC Tracker is disabled. Click <a href="#">here</a> to enable it.</th></thead>
						<thead class="text-center"><th>Stats</th><th>20 / 660 FCs</th><th>235% Avg. Speed</th><th>14,304,432 Acc. Score</th></thead>
						<thead class="text-center"><th>Stats</th><th class="Diff10">660 / 660 FCs</th><th>Speed <a href="#">disabled</a></th><th>Score <a href="#">disabled</a></th></thead>
					</table>
				</div>
				
				<!-- List -->
				<div class="row mx-auto" style="max-width: 932">
					<table class="table table-borderless">
					<?php
						foreach($list->fslist as $chapter=>$chsongs) {
							tableChapter(('Chapter ' . (($chapter)+1)));
							foreach($chsongs as $songArr)
								tableSong($songArr);
						}
					?>
					</table>
				</div>
			</div>
		</div>
	</div>
	
	<!-- jQuery, Popper, bootstrap.js, Local scripts -->
	<?=$html->links?>
</body>
</html>