<?php

//This library is for formatting and filtering text
function sanitizeGetVar($varname, $method=FILTER_SANITIZE_ENCODED) {
	if (isset($_GET[$varname])) return filter_var($_GET[$varname],$method);  else return '';
}

function sanitizePostVar($varname, $method=FILTER_SANITIZE_ENCODED) {
	if (isset($_POST[$varname])) return filter_var($_POST[$varname],$method); else return '';
}

//You would't necessarily call this function in your app. But it produces useful testing output.
function showExampleTable($unfilteredtext) {
	echo("<table>");
	$tabledata[1][0]="<tr><td>FILTER_SANITIZE_EMAIL</td><td>";				$tabledata[1][1]=filter_var($unfilteredtext,FILTER_SANITIZE_EMAIL); 			$tabledata[1][2]="</td></tr>";
	$tabledata[2][0]="<tr><td>FILTER_SANITIZE_ENCODED</td><td>";				$tabledata[2][1]=filter_var($unfilteredtext,FILTER_SANITIZE_ENCODED); 		$tabledata[2][2]="</td></tr>";
	$tabledata[3][0]="<tr><td>FILTER_SANITIZE_MAGIC_QUOTES</td><td>";			$tabledata[3][1]=filter_var($unfilteredtext,FILTER_SANITIZE_MAGIC_QUOTES); 	$tabledata[3][2]="</td></tr>";
	$tabledata[4][0]="<tr><td>FILTER_SANITIZE_SPECIAL_CHARS</td><td>";		$tabledata[4][1]=filter_var($unfilteredtext,FILTER_SANITIZE_SPECIAL_CHARS); 	$tabledata[4][2]="</td></tr>";
	$tabledata[5][0]="<tr><td>FILTER_SANITIZE_FULL_SPECIAL_CHARS</td><td>";	$tabledata[5][1]=filter_var($unfilteredtext,FILTER_SANITIZE_FULL_SPECIAL_CHARS); 	$tabledata[5][2]="</td></tr>";
	$tabledata[6][0]="<tr><td>FILTER_SANITIZE_STRING</td><td>";				$tabledata[6][1]=filter_var($unfilteredtext,FILTER_SANITIZE_STRING); 				$tabledata[6][2]="</td></tr>";
	$tabledata[7][0]="<tr><td>FILTER_SANITIZE_STRIPPED</td><td>";				$tabledata[7][1]=filter_var($unfilteredtext,FILTER_SANITIZE_STRIPPED); 			$tabledata[7][2]="</td></tr>";
	$tabledata[8][0]="<tr><td>FILTER_SANITIZE_URL</td><td>";					$tabledata[8][1]=filter_var($unfilteredtext,FILTER_SANITIZE_URL); 				$tabledata[8][2]="</td></tr>";
	$tabledata[9][0]="<tr><td>FILTER_UNSAFE_RAW</td><td>";					$tabledata[9][1]=filter_var($unfilteredtext,FILTER_UNSAFE_RAW); 					$tabledata[9][2]="</td></tr>";
	for ($i=1; $i <= 9; $i++) {
		//echo ($i);
		echo ($tabledata[$i][0] . $tabledata[$i][1] . $tabledata[$i][2]);
	}
}

function formatPhone($phone_string) {
	$retval=str_replace(" ","",$phone_string);
	$retval=str_replace("(","",$retval);
	$retval=str_replace(")","",$retval);
	$retval=str_replace("+","",$retval);
	$retval=str_replace("-","",$retval);
	if (substr($retval,1,1)=="1") $retval=substr($retval,2);
	if (strlen($retval)==10) $retval= substr($retval,0,3) . '-' . substr($retval,3,3) . '-' . substr($retval,6,4) ;
	return $retval;
}
 ?>

