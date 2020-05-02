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
	
	//Make sure no other code runs after this, without having to include it all in one big else
	exit;
}

//Parse input: Generate
if(isset($_GET['generate'])) {
	$list = new FSLister();
	$list->createList();
	echo json_encode($list->listID);
	exit;
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
					<button id="generator" class="btn btn-primary px-4 py-0">GENERATE</button>
				</div>
				<div class="row mt-4 d-flex justify-content-center">
					<h1 class="text-center">Randomize a full Guitar Hero career!<br/>
					Revisit the classics, ROCK ON!</h1>
				</div>
				<div class="row mt-4 d-flex justify-content-center">
					<a class="options"></a>
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
							<a href="#" target="_blank" class="link-unstyled"><div class="row text-nowrap"><p>Download The full GH Series Charts!</p></div></a>
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