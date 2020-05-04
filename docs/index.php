<?php
//Initialize
session_start();
require("includes/func_notepadTable.php");
require("php/database.php");
require("php/fslister.php");
require("php/error.php");

//HTML Object
class HTML {
	public $navbar, $scripts, $metalinks;
	public function __construct($dir) {
		$this->navbar = file_get_contents($dir . '/navbar.html');
	}
} $html = new HTML('./html');

//Parse input: UniqueID
if(isset($_GET['UniqueID'])) {
	//UniqueID is given and output variable is set
	if(isset($_GET['output'])) {
		$list = new FSLister();
		$success = $list->getHash($_GET['UniqueID']);
		switch($_GET['output']) {
			case 'hash': echo $list->hash; break; //Empty output if Invalid ID
			case 'validate': echo json_encode($success); break;
			default: error("No valid output type given.", true);
		}
	} else require("fctracker.php"); //UniqueID is given but no output requested, load fctracker.php
	exit;
}

//Parse input: Generate
if(isset($_GET['generate'])) {
	$list = new FSLister();
	$list->createList();
	
	//Parse options
	if(isset($_POST['options']))
		foreach(json_decode($_POST['options']) as $key=>$value)
			$list->$key = $value;
	
	//Output ID
	echo json_encode($list->listID);
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
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/fsrandomizer.css">
	<title>FSRandomizer</title>
</head>
<body>
	<!-- Content -->
	<div class="container" style="height: 100vh">
		<!-- Navbar -->
		<div class="row h-25 pt-4 align-items-top">
			<?=$html->navbar?>
		</div>
		
		<!-- Generation -->
		<div class="row h-50 align-items-center">
			<div class="container mt-6">
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
					<div class="modal fade" id="optionsMenu">
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
										<div class="row d-flex justify-content-center">
											<button type="button" class="btn btn-primary mr-4" id="reset">Reset</button>
											<button type="button" class="btn btn-primary ml-4" data-dismiss="modal">Close</button>
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
							<a href="#" target="_blank" class="link-unstyled">
								<div class="row mb-2 text-nowrap">
									<div class="col p-0 text-nowrap"><b>Download the Charts</b></div>
									<div class="col p-0 text-right align-self-center d-none d-lg-block"><img src="./images/+.png" /></div>
								</div>
							</a>
							<div class="row text-nowrap"><p><a href="#" target="_blank">Download</a> the full GH Series Charts!</p></div>
							<div class="row text-nowrap"><p>Click <a href="#" target="_blank">here</a> for DLC and <a href="#" target="_blank">here</a> for extra original content.</p></div>
						</div>
					</div>
					<div class="col-md-4 pb-5">
						<div class="container">
							<a href="#" target="_blank" class="link-unstyled">
								<div class="row mb-2 text-nowrap">
									<div class="col p-0 text-nowrap"><b>Your list in Clone Hero</b></div>
									<div class="col p-0 text-right align-self-center d-none d-lg-block"><img src="./images/+.png" /></div>
								</div>
							</a>
							<div class="row text-nowrap"><p>Download our <a href="#" target="_blank">App</a> to bring your list to Clone Hero!</p></div>
							<div class="row text-nowrap"><p>It's <a href="https://github.com/rafaelgpires/FSRandomizer-App/" target="_blank">Open Source</a> and requires using <a href="#" target="_blank">these charts</a>.</p></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- jQuery, Popper, bootstrap.js, Local scripts -->
	<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	<script src="./js/fsrandomizer.js"></script>
</body>
</html>