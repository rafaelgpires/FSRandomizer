<?php
/*
 * function notepadTable()
 *
 * Function: 		Create a notepad table from a 2-dimensional array
 *	 
 * Syntax:  		notepadTable(
 *	 			$table as array(),					An array to turn to notepad, or a notepadTable string to turn to an array
 *				[$func as string = 'notepad'(|'table')],		Convert array to notepadTable or vice-versa
 *				[$prefix as string = null(|custom prefix string)]	Add / Parse prefix
 *	 		)
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