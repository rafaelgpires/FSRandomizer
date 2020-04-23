<?php
/*
 * function debug()
 * 
 * Author: 	Rafael Pires
 * Date:	Jan 2020
 * 
 * Function:		Debug Text Output
 * 
 * Syntax:			function debug(
 *						$text as string,						Text of the error message
 *						[$type as string],						Type of message
 *						[$title as string=null],				Title of the error message, keep null to remove prefix
 *						[$inout as string=null(|'in'|'out')],	Is it related to input, output or neither?
 *						[$newline as bool]						Do we create a newline or continue this message later?
 *					)
 *				
 * Description:		Debug allows us to choose whether to display error messages or logs,
 *					remove them altogether, change their format, etc., outputs with print().
 *				
 * Notes:
 *					Recommend keeping $title the same length as all others for good aesthetics.
 * 
 * Example of usage:
 *		$debug = 'log';
 *		debug('Just started this script!'); 			//Prints " -- Just started this script!\n";
 *		debug('Testing some functions...', 'debug'); 	//Doesn't print anything due to $debug level
 *		debug('Cool file bro!', 'log', 'FILE', 'in'); 	//Prints " << FILE: Cool file bro!\n"
 */
 
function debug($text, $type='log', $title=null, $inout = null, $newline = true) {
	//Load debug level
	global $debug;
	if(!isset($debug)) $debug = 'log'; //Default to 'log'
	
	//Check whether we should output this message
	switch($debug) {
		case 'none': return; break;
		case 'log': if($type == 'debug') return; break;
		case 'debug': break;
		default: die('ERROR: Incorrect value for $debug for the debug() function.');
	}
	
	//Output the debug message
	$inout = ($inout == 'in' ? '<<' : ($inout == 'out' ? '>>' : null));
	print(
		($title ? (
			($inout ? "  $inout " : '  -- ' ) .
			strtoupper($title) . ': '
		) : '') .
		$text .
		($newline ? "\n" : '')
	);
}
?>