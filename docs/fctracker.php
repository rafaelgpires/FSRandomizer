<?php
/* Page: FCTracker
 *
 * Description:		This page shows the $list->fslist and allows the user to interact with it.
 * Page Dependencies:	HTML $html, bool $logged, FSList $list
 */
class FCTracker {
	#Properties
	private $database;			//Instance of SQLConn
	public $list;				//Dependency Injection
	public $fchash;				//Database: fchash
	public $fctracker	= false;	//Database: fctracker
	public $fcs2diff;			//Internal: Thresholds for difficulty icons/colors
	
	public $fccount		= 0;		//Output: Amount of FCs
		
	#Methods
	public function __construct($list) {
		//Dependencies
		$this->database = new SQLConn();
		$this->list     = $list;
		if(!$this->list->readHash()) error('FCTracker was loaded without a valid list.', true); //Needs a valid list
		
		//Construct
		foreach(($this->database->fcRead($this->list->listID)) as $key=>$value) $this->$key = $value;	//Load FC values from the database
		$this->database->visitInc($this->list->listID, ++$this->list->listVisits);			//Increment visits
		if($this->fctracker) {
			//FC Count
			if($this->fchash) {
				//There's an FCHash, so the user has enabled it before, look for FCs
				for($i=0; $i<660; $i++)
					if($this->fchash[$i] == '1')
						$this->fccount++;
			} else {
				//There isn't an FCHash yet, so the user has just enabled it, create one
				$this->fchash = str_repeat('0', 660);					//No FCs
				$this->database->update('fchash', $this->fchash, $this->list->listID);	//Store it
			}
			
			//Add FC bool and count to the fslist array
			$songcount = 0;
			foreach($this->list->fslist as $chapter=>$chaptersongs) {
				foreach($chaptersongs as $key=>$song) {
					$this->list->fslist[$chapter][$key]['count'] = $songcount;						//Position of the FC in FCHash
					$this->list->fslist[$chapter][$key]['fc']    = ($this->fchash[$songcount] == '1') ? true : false;	//FC bool
					$songcount++;
				}
			}
		}
		
		//Set thresholds
		$this->fcs2diff = array(
			0   => 1, // 0  FCs = Diff 1
			1   => 2, // 1  FCs = Diff 2
			132 => 3, //20% FCs = Diff 3
			264 => 4, //40% FCs = Diff 4
			330 => 5, //50% FCs = Diff 5
			396 => 6, //60% FCs = Diff 6
			462 => 7, //70% FCs = Diff 7
			528 => 8, //80% FCs = Diff 8
			594 => 9, //90% FCs = Diff 9
			660 => 10, // FSFC  = Diff 10
		);
	}
	public function convertFCs2Diff($fccount):int {
		//This function exists client-side (fsrandomizer.js)
		$difficulty = 0;
		foreach($this->fcs2diff as $threshold=>$diff)
			if($fccount >= $threshold)
				$difficulty = $diff;
		
		return $difficulty;
	}
	public function tableChapter($chapter) { echo "<tr><td colspan=3 class=\"chapter\">$chapter</td></tr>\n"; }
	public function tableSong($songArr) {
		//Check for encores
		$song = preg_replace_callback("/^(\[(ENCORE)\] |\[(SUPER ENCORE)\] )/", function($encore){
			global $songArr;
			if(isset($encore[3])) {
				//Super Encore, check for difficulty icon
				if($songArr[0] >= 5) return '<img src="./images/diff_'.$songArr[0].'.png" class="diffIcon" /><b>Super Encore</b>: ';
				else return '<b>Super Encore</b>: ';
			} else return '<b>Encore</b>: '; //Encore
		}, $songArr[1]); //No encore
		
		//Check for FC
		if($this->fctracker) { 
			$FC = '<a data-count="' . ($songArr['count']+1) . '" class="' . ($songArr['fc'] ? 'FC' : 'NoFC') . '">&nbsp;</a>';
		} else $FC = null;
		
		//Add difficulty colors
		$diff = $songArr[0];
		$diff = "<span class=\"Diff$diff\">$diff / 10</span>";
		
		//Set game
		$game = $songArr[2];
		echo <<<EOL
			<tr>
				<td>$song</td>
				<td>$FC</td>
				<td>$diff</td>
				<td>$game</td>
			</tr>
EOL;
	}
} $FCTracker = new FCTracker($list);
?>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<?=$html->styles?>
	<title>FSRandomizer - FC Tracker</title>
	<script>var ListID="<?=$FCTracker->list->listID?>";</script>
	<script>var logged=<?=json_encode($logged)?>;</script>
	<script>var fcs2diff=JSON.parse('<?=json_encode($FCTracker->fcs2diff)?>');</script>
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
							<?php if($FCTracker->convertFCs2Diff($FCTracker->fccount) >= 5) echo '<img src="./images/diff_'.$FCTracker->convertFCs2Diff($FCTracker->fccount).'.png" />'; ?>
						</div>
						<div class="row listtitle">
							<h1 id="listname" data-show="#inputName"><?=$FCTracker->list->listName?></h1>
							<input ="text" class="listname" value="<?=$FCTracker->list->listName?>" id="inputName" data-show="#listname" data-name="name" maxlength=13 />
						</div>
						<div class="row listdescription">
							<h2 id="listdesc" data-show="#inputDesc"><?=$FCTracker->list->listDesc?></h2>
							<input ="text" class="listdesc" value="<?=$FCTracker->list->listDesc?>" id="inputDesc" data-show="#listdesc" data-name="desc" maxlength=45 />
						</div>
						<div class="row listfiller">&nbsp;</div>
					</div>
				</div>
				
				<!-- Alerts -->
				<div class="row mx-auto listalerts">
					<table class="table m-0">
						<?php if($FCTracker->list->listVisits == 1) { ?> <thead><th colspan=4 class="passalert">Your password is: <span class="pass"><?=$FCTracker->list->listPass?></span> - Save it, this is the last time you'll see it.</th></thead> <?php } ?>
						<?php if(!$FCTracker->fctracker) { ?><thead><th colspan=4>Your FC Tracker is disabled. Click <a href="#" id="enable_tracker">here</a> to enable it.</th></thead><?php } else { ?>
							<thead class="text-center">
								<th id="disable_tracker" class="thlink">Stats</th>
								<th class="<?=("Diff".$FCTracker->convertFCs2Diff($FCTracker->fccount))?>"><span id='fccount'><?=$FCTracker->fccount?></span> / 660 FCs</th>
								<th>Speed <a href="#">disabled</a></th>
								<th>Score <a href="#">disabled</a></th>
							</thead>
						<?php } ?>
					</table>
				</div>
				
				<!-- List -->
				<div class="row mx-auto listsongs">
					<table class="table table-borderless">
					<?php
						foreach($FCTracker->list->fslist as $chapter=>$chsongs) {
							$FCTracker->tableChapter(('Chapter ' . (($chapter)+1)));
							foreach($chsongs as $songArr)
								$FCTracker->tableSong($songArr);
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