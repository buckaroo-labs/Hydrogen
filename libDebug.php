<?php
//Debug output path DEBUG_PATH is defined in settings.php
require_once ('settings.php');
function debug($info){
	if (DEBUG) {

		$output[0]=date("Y-m-d H:i:s");
		$output[1]=$_SERVER['REMOTE_ADDR'];
		if (isset($_SESSION['username'])) $output[2]=$_SESSION['username']; else $output[2]='null';
		$output[3]=$info;
		$output[4]=$_SERVER['REQUEST_URI'];
		$fp = fopen(DEBUG_PATH, 'a');
		fwrite($fp, implode("\t | ",$output) . "\n");
		fclose($fp);
	}
}

?>