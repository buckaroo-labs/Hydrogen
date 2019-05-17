<?php
include_once ('Hydrogen/libFilter.php');

//This library is used to maintain state between page clicks.
//See e.g. libPagination.php

//declare a list of GET variables to be maintained and sanitized
if (!isset($stateVarList)) $stateVarList=array('sortorder','userid','productid');
$arrlength = count($stateVarList);
$stateVar=array();

//Use libFilter.php to sanitize the GET variables enumerated above
for($x = 0; $x < $arrlength; $x++) {
    $stateVar[$stateVarList[$x]] = sanitizeGetVar($stateVarList[$x]);
}

if (isset($_GET["pagenum"]))  {
	$page_num=sanitizeGetVar("pagenum");
} else $page_num=1;

//The output of this function is meant to be appended to links within the application.
function newVars($pg,$oldvar=array()) {
	global $stateVar;
	if (count($oldvar)==0) $oldvar=$stateVar;
	$retval="?pagenum=" . $pg;
	foreach ($oldvar as $key => $value) {
		if (isset($value)) {
			if ($value!="") $retval=$retval . "&" . $key  . "=" . $value ;
		}
	}
	return $retval;
}



?>