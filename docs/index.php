<?php
include("includes/func_notepadTable.php");
include("database.php");
include("fslister.php");

if(isset($_GET['uniqueID']) && isset($_GET['output'])) {
	$list = new \Lister\FSLister();
	$list->getList($_GET['uniqueID']);
	switch($_GET['output']) {
		case 'hash': die($list->hash); break;
	}
} else {
	$list = new \Lister\FSLister();
	$list->createList();
}
?>