<?php
include_once ('lib/filter.php');

$stateVarList=array('sort','servicetype','motsid','envid','applist','brand','supportteam','dashboard');
$stateVar=array();

$arrlength = count($stateVarList);
for($x = 0; $x < $arrlength; $x++) {
    $stateVar[$stateVarList[$x]] = sanitizeGetVar($stateVarList[$x]);
}

if (isset($_GET["pagenum"]))  {
	$page_num=sanitizeGetVar("pagenum");
} else $page_num=1;


function newVars($pg,$oldvar=array()) {
	global $stateVar;
	if (count($oldvar)==0) $oldvar=$stateVar;
	$retval="?pagenum=" . $pg;
	foreach ($oldvar as $key => $value) {
		if (isset($value)) {
			$retval=$retval . "&" . $key  . "=" . $value ;
		}
	}
	return $retval;
}



?>