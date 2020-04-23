<?php
/*
 * function parseHTMLtable()
 *
 * Author:	Rafael Pires
 * Date:	Jan 2020
 * 
 * Function:    	HTML Table Parser
 *
 * Syntax: 			function parseHTMLtable(
 *						$DOMTable as DOMNode, 
 *						[$output as string = 'node'(|'text'|'html')]
 *					)
 *
 * Description: 	The function is meant to parse a standard HTML table that includes rowspans.
 *              	While accounting for the rowspan parameter it creates a standard 2D array of
 *              	the table's data, the data can be returned as a DOMNode, text or HTML.
 * 
 * Paramaters:   
 *              	$DOMTable         - The DOMNode corresponding to the <tbody> of the table
 *              	[$output]         - [OPTIONAL] String option, default: 'node'
 *											'node' - Output with table cell data as a DOMNode
 *											'text' - Output with table cell data as text
 *											'html' - Output with table cell data as HTML
 * 
 * Return:
 *              	array(            - 2D Array of the table's data
 *						0 => array(0 => $data, 1 => $data, ...) - First dimension are rows
 *						1 => array(0 => $data, 1 => $data, ...) - Second dimension are cols
 *					);
 *
 * Example of usage:
 *		$HTMLDoc    = new DOMDocument();
 *		$HTMLDoc->loadHTMLFile('mytables.html');
 *		$myTableDOM = $HTMLDoc->getElementsByTagName('tbody')->item(0);
 *		$myTable    = parseHTMLtable($myTableDOM, 'text');
 *		
 *		//Return first value of the table
 *		print($myTable[0][0]);
 *
 */
 
function parseHTMLtable($DOMTable, $output = 'node') {
	# Declarations
	$XPath    = new DOMXPath($DOMTable->ownerDocument); //Load XPath
	$DOMTable = $DOMTable->firstChild; 					//Move to the first element
	$table    = array();               					//The output array
	$rowcount = 0;                     					//Keep track of the row
	$colcount = 0;                     					//Keep track of the col
	
	# Check options
	if(!in_array($output, array('node', 'text', 'html'))) {
		trigger_error('Unrecognised option for $output');
		return null;
	}
	
	# Start looping through <tr>s
	do {
		$tr = $DOMTable; //First value should be a <tr>
		if($tr->nodeName != 'tr') continue; //If not, keep moving

		//Now we loop through the <td>s
		$values       = array();
		$containsdata = false;
		$colcount     = 0;
		if($tr->hasChildNodes()) {
			//We expect <td>s
			foreach($tr->childNodes as $td) {
				if($td->nodeName != 'td') continue; //If not, keep moving
				$containsdata = true; //If we didn't continue, there's a <td>
				$values[$rowcount][$colcount] = $td; //Save value to add later
				
				//Check for rowspans
				$rowspan = $XPath->evaluate('string(@rowspan)', $td);
				if($rowspan)
					//There's a rowspan, add the same value to the following rows
					for($i=0; $i<$rowspan; $i++)
						$values[$rowcount+$i][$colcount] = $td;
				
				//Next column
				$colcount++;
			} if(!$containsdata) continue; //Empty <tr>s are skipped
			
			//Now we can add values
			foreach($values as $row=>$datacols) {
				foreach($datacols as $col=>$data) {
					//If values already exist in this column, it's been added by a rowspan
					//so we move to the next column
					while(isset($table[$row][$col])) $col++;
					
					//At this point, this particular table cell is open, so we assume
					//that's where this new information should be in
					switch($output) {
						case 'node': break;
						case 'text': $data = $data->textContent; break;
						case 'html': $data = $data->ownerDocument->saveHTML($data);
					} $table[$row][$col] = $data; //Store data
				}
			}
			
			//It happens that sometimes we set a value for a non-first column of a row before the first
			//due to the rowspans, which ends with a column like this: array(3 => $data, 0 => $data, 1...)
			//To fix it, we simply sort it by keys, so that a foreach would start at [0] and not [3]
			ksort($table[$rowcount]);
		} $rowcount++;
	} while($DOMTable = $DOMTable->nextSibling);
	
	# Output
	return $table;
}
?>