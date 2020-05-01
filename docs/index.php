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
} $html = new HTML('html');

//Parse input
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
	die();
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
	<!-- Navbar -->
	<?=$html->navbar?>
	
	<!-- jQuery, Popper, bootstrap.js, Local scripts -->
	<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	<script src="js/fsrandomizer.js"></script>
</body>
</html>