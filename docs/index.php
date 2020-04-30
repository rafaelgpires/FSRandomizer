<?php
//Initialize
session_start();
require("includes/func_notepadTable.php");
require("database.php");
require("fslister.php");
require("error.php");

//Parse input
if(isset($_GET['uniqueID'])) {
	//Unique is given and output variable is set
	if(isset($_GET['output'])) {
		$list = new FSLister();
		$list->getList($_GET['uniqueID']);
		switch($_GET['output']) {
			case 'hash': echo $list->hash; break;
			default: error("No valid output type given.", true);
		}
	} else require("fctracker.php"); //Unique is given but no output requested, load fctracker.php
	
	//Make sure no other code runs after this, without having to include it all in one big else
	die();
}

//Landing page
?>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="fsrandomizer.css">
	<title>FSRandomizer</title>
</head>
<body>
	<!-- Navbar -->
	<div class="container mt-4">
		<nav class="navbar navbar-expand navbar-light bg-light">
			<a class="navbar-brand mr-auto" href="./">FSRandomizer</a>
			<div class="navbar-nav">
				<a class="nav-item nav-link" href="#">BUGS</a>
				<a class="nav-item nav-link" href="#">CONTACT</a>
			</div>
			<form class="form-inline ml-2">
				<div class="input-group input-group-sm">
					<div class="input-group-prepend"><span class="input-group-text">#</span></div>
					<input type="text" class="form-control" placeholder="List ID" name="UniqueID" size="13">
				</div>
			</form>
		</nav>
	</div>
	
	<!-- jQuery, Popper, bootstrap.js -->
	<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>