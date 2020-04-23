<?php
/*
 * function notepadTable()
 *
 * Author: 	Rafael Pires
 * Date:	Jan 2020
 *
 * Function: 		Create a notepad table from a 2-dimensional array
 *	 
 * Syntax:  		notepadTable(
 *	 			$table as array(),
 *				[$func as string = 'notepad'(|'table')],
 *				[$prefix as string = null(|custom prefix string)]
 *	 		)
 *	
 * Description:		The function picks up a 2 dimensional array and creates a .txt file with the
 *			information easily visualised by the end user as a table. Alternatively, it can
 *			load such a file, created by the function, and output the 2D Array back.
 *	 
 * Paramaters:	
 *			$table			- The data that represents the table, expected value depends on $func
 *			$func = 'notepad' 	- 2D Array of the table's data, i.e.:
 *		         		  	array(
 *							0 => array(0 => $data, 1 => $data, ...) - First dimension are rows
 *							1 => array(0 => $data, 1 => $data, ...) - Second dimension are cols
 *						);
 *			$func = 'table' 	- $table should be a text string created by this function
 *											
 *			[$func]			- [OPTIONAL] String option, default: 'notepad'
 *				'notepad' - Outputs a string with the contents of the textfile
 *				'table'   - Outputs a 2D Array representing the input table
 *	 
 *			[$prefix]		- [OPTIONAL] String option, default: null
 *				Adds a prefix to each line in the text table, or ignores the prefix from each
 *				line in a text file created by this function, use '#row#' to represent the
 *				row ID or line number.
 *
 * Example of usage:
 *		$array = array(
 *				0 => array(0 => 'Some info', 1 => 'Some info'), 
 *				1 => array(0 => 'Next info', 1 => 'Next info')
 *			);
 *		
 *		//Write it into a file
 *		$textTable = notepadTable($array);
 *		file_put_contents('./MyTable.txt', $textTable);
 *
 *		//Turn a file into an array
 *		$file = file_get_contents('./MyTable.txt');
 *		$sameArray = notepadTable($file, 'table');
 *
 */

namespace Includes;
function notepadTable($table, $func='notepad', $prefix = null) {
	switch($func) {
		case 'notepad': 
			# Count max length of each column
			$length = array();
			foreach($table as $row=>$rowdata)
				foreach($rowdata as $col=>$data)
					if(!isset($length[$col]) || ($length[$col] < mb_strlen($data)))
						$length[$col] = mb_strlen($data);
					
			# Create an easily parseable string to display on a text file
			$tablefile = '';
			foreach($table as $row=>$rowdata) {
				//Add whitespaces to fix col length
				foreach($rowdata as $col=>$data)
					$rowdata[$col] = $data . str_repeat(' ', ($length[$col] - mb_strlen($data)));
					
				//Write it in a single line
				$tablefile .= ($prefix ? str_replace('#row#', ($row+1), $prefix) : '') . implode(' | ', $rowdata) . "\n";
			}
			
			# Output
			return $tablefile;
			
		case 'table':
			# Explode the string into an array
			$arrTable = array();
			$rows = explode("\n", $table);
			foreach($rows as $row=>$rowdata) {
				$cols = explode(' | ', $rowdata);
				
				//If there's no data, ignore it
				if(!isset($cols[0]) || empty($cols[0])) continue;
				
				//Remove prefix from the array
				if($prefix)
					$cols[0] = preg_replace(('/^' . preg_quote($prefix) . '/'), '', $cols[0]);
				
				//Write the array
				foreach($cols as $col=>$data)
					$arrTable[$row][$col] = trim($data);
			}
			
			# Outputs
			return $arrTable;
			
		default: die('Invalid $func paramater for notepadTable()');
	}
}
?>