<?php
/*
 * function error()
 * 
 * Function:	Log errors and deal with them on a user level
 * 
 * Syntax:	function error(
 *			$message as string,	Text of the error message
 *			$die as bool,		Whether to return a complete error page and prevent further code execution or just trigger the error as usual
 *		)
 */
function error($message, $die) {
	if(!$die) { trigger_error($message); }
	else {
?>
TODO: Error Page, this was procced with: <?=$message?>
<?php
	}
}
?>