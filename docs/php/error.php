<?php
/*
 * function error()
 * 
 * Function:	Log errors and deal with them on a user level
 *		This function should be called from within index.php and assumes $html has been declared
 *		$die = true; assumes no output has been given to the user yet
 * 
 * Syntax:	function error(
 *			$message as string,	Text of the error message
 *			$die as bool,		Whether to return a complete error page and prevent further code execution or just trigger the error as usual
 *		)
 */
function error($message, $die) {
	
	if(!$die) { trigger_error($message); }
	else {
		global $html;
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
		
		<!-- Error -->
		<div class="row h-50 align-items-center">
			<div class="container mt-6">
				<div class="row d-flex justify-content-center">
					<h1 class="text-center">Uh oh... Error!<br/>
				</div>
				<div class="row mt-4 d-flex justify-content-center">
					<h1 class="text-center descriptor"><?=$message?></h1>
				</div>
				<div class="row mt-4 d-flex justify-content-center">
					<img src="./images/broken_guitar.png" />
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
<?php
		exit;
	}
}
?>