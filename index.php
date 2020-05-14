<?php
//Initialize
session_start();
require("includes/func_notepadTable.php");
require("php/database.php");
require("php/fslister.php");
require("php/error.php");
require("php/html.php");
$database = new SQLConn();
$html     = new HTML();

//Check session
$logged = false;
if(isset($_SESSION['logged']) && isset($_SESSION['name']))
	$logged = array($_SESSION['logged'], $_SESSION['name']);

//Parse input: UniqueID
if(isset($_GET['UniqueID'])) {
	//UniqueID is given, check validity before continuing
	$list    = new FSLister();
	$success = $list->getList($_GET['UniqueID']);
	
	//Parse other options
	if(isset($_GET['output'])) {
		//Output has been requested
		switch($_GET['output']) {
			case 'hash': echo $list->listHash; break; //Empty output if Invalid ID
			case 'logout': unset($_SESSION['logged'], $_SESSION['name']); //Empty output
			case 'validate': echo json_encode($success); break;
			case 'validatepass':
				if(!isset($_GET['pass'])) error('Login attempt without password.');
				$login = $database->login($list->listID, $_GET['pass']);
				if($login) { 
					$_SESSION['logged'] = $list->listID; 
					$_SESSION['name']   = $list->listName; 
				} echo json_encode($login);
				break;
			default: error("No valid output type given.", true);
		}
	} elseif($success) require("fctracker.php"); //UniqueID is valid and there's no output request, load fctracker.php
	else error('Invalid list ID!', true); //UniqueID is invalid and there's no output request, show error
	exit;
}

//Parse input: Generate
if(isset($_GET['generate'])) {
	$list = new FSLister();
	
	//Parse options
	if(isset($_POST['options']))
		foreach(json_decode($_POST['options']) as $key=>$value)
			if(in_array($key, array('nsongs', 'variance', 'encore', 'encorebonus', 'superencore', 'superencorebonus', 'resetencores')))
				$list->$key = intval($value);
	
	//Create List, Login and output ID
	$list->createList();
	$_SESSION['logged'] = $list->listID;
	$_SESSION['name']   = $list->listName;
	echo json_encode($list->listID);
	exit;
}

//Parse input: Update
if(isset($_GET['update'])) {
	//Check if it's a valid request
	if(!$logged) 									error('You\'re not logged in.', true);
	if(!isset($_POST['UniqueID'])) 							error('You didn\'t tell me which list you wanna edit.', true);
	if($logged[0] != $_POST['UniqueID']) 						error('You\'re not logged in the list you want to edit.', true);
	if(!isset($_POST['name']) || !isset($_POST['value'])) 				error('Error: Don\'t know what you want to update.', true);
	
	//Get list
	$list    = new FSLister();
	if(!$list->getList($_POST['UniqueID'])) 					error('Invalid list ID on update request.', true);
	
	//Parse input
	$value = trim($_POST['value']);
	$name  = trim($_POST['name']);
	switch($name) {
		case 'name':
			//Filter
			if(strlen($value) > 13 || strlen($value) < 1) 			error('Name is not within the character limit (1-13).', true);
			if(!preg_match('/^\w+([ -_]\w+)*$/', $value)) 			error('Name is invalid (alphanumerical only with spaces/hypens/underscores)', true);
			
			//Update user's session with new name
			$_SESSION['name'] = $value;
			break;
			
		case 'desc':
			//Filter
			if(strlen($value) > 45 || strlen($value) < 1) 			error('Description is not within the charater limit (1-45).', true);
			if(!preg_match('/^\w+([ -_]\w+)*$/', $value)) 			error('Description is invalid (alphanumerical only with spaces/hypens/underscores)', true);
			break;
			
		case 'fctracker':
			//Filter
			if($value != 0 && $value != 1)					error('Invalid value for FCTracker!', true);
			break;
			
		case 'unlocker':
			//Filter
			$value = json_decode($value); //Expecting a JS boolean
			if(!is_bool($value))						error('Invalid value for unlocker!', true);
			$value = $value ? 1 : 0; //Convert to TINYINT
			break;
			
		case 'speeder': case 'scorer':
			//Filter
			if($value != 0 && $value != 1)					error('Invalid value for Speed tracker.', true);
			break;
		
		//FC Array Update
		case 'FC': $FC = true;
		case 'NoFC':
			//Filter
			if(!isset($FC)) $FC = false;
			$value = intval($value);
			if(!$value)							error('Invalid value for FC!', true);
			if($value < 1 || $value > 660)					error('Invalid value range for FC!', true);
			
			//Get array
			$fcvars = $database->optRead($logged[0]);
			if(!$fcvars['fctracker'])					error('FC Tracker isn\'t enabled in this list.', true);
			if(!$fcvars['fchash'])						error('FC Hash hasn\'t been created for this list.', true);
			
			//Update array and col/val
			$name  = 'fchash';
			$value = substr_replace($fcvars['fchash'], ($FC ? '1' : '0'), ($value-1), 1);
			break;
		
		//Speed/Score Array Update
		case 'speed': case 'score':
			//Filter
			if($value < 1 || $value > 660)					error('Invalid ID range.', true);
			if(!isset($_POST[$name]))					error('Value not given.', true);
			if(!isset($_POST['proof']))					error('Proof value not given.', true);
			$sval = intval(str_replace("%", "", $_POST[$name]));
			$proof = $_POST['proof'];
			if(!is_int($sval))						error('Invalid value.', true);
			if($name == 'speed') if($sval < 100 || $sval > 999)		error('Speed value out of range.', true);
			if($name == 'score') if($sval < 1 || $sval > 9999999)		error('Score value out of range.', true);
			if(!empty($proof) && !filter_var($proof, FILTER_VALIDATE_URL))	error('Invalid proof value.', true);
			
			//Get array
			$fcvars = $database->optRead($logged[0]);
			if($fcvars[$name]) $speedArr = json_decode($fcvars[$name], true);
			else $speedArr = array();
			
			//Update array and col/val
			$speedArr[($value-1)] = array($sval, $proof);
			$value = json_encode($speedArr);
			break;
			
		default:
											error('Unrecognised value name.', true);
	}
	
	//Update database
	$database->update($name, $value, $list->listID);
	exit;
}

//Parse input: Common HTTP Errors
if(isset($_GET['http_error'])) {
	switch($_GET['http_error']) {
		case '400': error("Bad request<br />What's your browser doin'?", true); break;
		case '403': error("Forbidden<br />Yo, get outta here.", true); break;
		case '404': error("404<br />These aren't the pages you're looking for.", true); break;
		case '500': error("Internal Error<br />I fucked up somewhere, sorry.", true); break;
		default: error("You just wanted to see the error page, huh?", true);
	} exit;
}

//Landing page
?>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<?=$html->styles?>
	<title>FSRandomizer</title>
</head>
<body>
	<!-- Content -->
	<div class="container vh-100">
		<!-- Navbar -->
		<div class="row h-25 pt-4 align-items-top">
			<?=$html->navbar?>
		</div>
		
		<!-- Generation -->
		<div class="row h-50 align-items-center">
			<div class="container">
				<div class="row d-flex justify-content-center">
					<div class="alert d-none" role="alert" id="alert"></div>
				</div>
				<div class="row d-flex justify-content-center">
					<button id="generator" class="btn btn-primary generate px-4 py-0">GENERATE</button>
				</div>
				<div class="row mt-4 d-flex justify-content-center">
					<h1 class="text-center descriptor">Randomize a full Guitar Hero career!<br/>
					Revisit the classics, ROCK ON!</h1>
				</div>
				<div class="row mt-4 d-flex justify-content-center">
					<a class="options" id="options"></a>
					<div class="modal fade" tab-index="-1" id="optionsMenu">
						<div class="modal-dialog modal-dialog-centered modal-sm" role="document">
							<div class="modal-content">
								<div class="modal-header"><h5 class="modal-title w-100 text-center">OPTIONS</h5></div>
								<div class="modal-body">
									<div class="container-fluid" id="optionMenu">
										<div class="row">
											<div class="col-6">
												<div class="form-group text-center">
													<label for="nsongs" class="option">Songs<br />(per Chapter)</label>
													<input type="text" class="form-control text-center" id="nsongs" value="15" />
												</div>
											</div>
											<div class="col-6">
												<div class="form-group text-center">
													<label for="variance" class="option">Difficulty <br />% Variance</label>
													<input type="text" class="form-control text-center" id="variance" value="25%" />
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-6">
												<div class="form-group text-center">
													<label for="encore" class="option">Difficult Encore<br />(% Chance)</label>
													<input type="text" class="form-control text-center" id="encore" value="100%" />
												</div>
											</div>
											<div class="col-6">
												<div class="form-group text-center">
													<label for="encorebonus" class="option">Difficult Encore<br />(% Bonus)</label>
													<input type="text" class="form-control text-center" id="encorebonus" value="10%" />
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-6">
												<div class="form-group text-center">
													<label for="superencore" class="option">Super Encore<br />(% Chance)</label>
													<input type="text" class="form-control text-center" id="superencore" value="20%" />
												</div>
											</div>
											<div class="col-6">
												<div class="form-group text-center">
													<label for="superencorebonus" class="option">Super Encore<br />(% Bonus)</label>
													<input type="text" class="form-control text-center" id="superencorebonus" value="25%" />
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-12">
												<div class="form-group form-check">
													<input type="checkbox" class="form-check-input" id="resetencores" />
													<label for="resetencores" class="form-check-label option">Reset Encores<br /><span class="check-label">(When difficulty procs, reset it for next encore)</span></label>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-12">
												<div class="alert alert-info alert-dismissable fade show optionalert" role="alert">
													The sum of variance + bonus cannot be above 100%.
													<button type="button" class="close" data-dismiss="alert" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<div class="container-fluid">
										<div class="row mb-3" id="optionDesc">
											<p>List will be made with <span class="info">44 chapters</span>.</p>
											<p>Each chapter has <span class="info">3 encores</span>.</p>
											<p>Songs fluctuate within <span class="info">25% difficulty</span>.</p>
											<p>Encores can (<span class="info">100%</span>) be <span class="info">10% harder</span>.</p>
											<p>Supers (<span class="info">20%</span>) are <span class="info">25% harder</span>.</p>
											<p><span class="info">Consistent</span> difficulty within encores.</p>
										</div>
										<div class="row d-flex justify-content-end">
											<button type="button" class="btn btn-primary" data-dismiss="modal">Done</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Links -->
		<div class="row h-25 pb-5 align-items-end">
			<div class="container">
				<div class="row justify-content-around">
					<div class="col-md-4 pb-5">
						<div class="container">
							<a href="https://drive.google.com/open?id=10bUgi6oxG1wiYg1R41-G6oD-lzUEmlzz" target="_blank" class="link-unstyled">
								<div class="row mb-2 text-nowrap">
									<div class="col p-0 text-nowrap"><b>Download the Charts</b></div>
									<div class="col p-0 text-right align-self-center d-none d-lg-block"><img src="./images/+.png" /></div>
								</div>
							</a>
							<div class="row text-nowrap"><p><a href="https://drive.google.com/open?id=10bUgi6oxG1wiYg1R41-G6oD-lzUEmlzz" target="_blank">Download</a> the full GH Series Charts!</p></div>
							<div class="row text-nowrap"><p>Click <a href="https://drive.google.com/open?id=1LAD7GzsTKqUOZWVauVvdq4JuHhndTKlf" target="_blank">here</a> for DLC and <a href="https://drive.google.com/open?id=1ZO39MbjqqU_UxEn6vsL6c8Bnna_r9rMF" target="_blank">here</a> for extra original content.</p></div>
						</div>
					</div>
					<div class="col-md-4 pb-5">
						<div class="container">
							<a href="./FSRandomizer.exe" target="_blank" class="link-unstyled">
								<div class="row mb-2 text-nowrap">
									<div class="col p-0 text-nowrap"><b>Your list in Clone Hero</b></div>
									<div class="col p-0 text-right align-self-center d-none d-lg-block"><img src="./images/+.png" /></div>
								</div>
							</a>
							<div class="row text-nowrap"><p>Download our <a href="./FSRandomizer.exe" target="_blank">App</a> to bring your list to Clone Hero!</p></div>
							<div class="row text-nowrap"><p>It's <a href="https://github.com/rafaelgpires/FSRandomizer-App/" target="_blank">Open Source</a> and requires using <a href="./Original Series.zip" target="_blank">these charts</a>.</p></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- jQuery, Popper, bootstrap.js, Local scripts -->
	<?=$html->links?>
</body>
</html>