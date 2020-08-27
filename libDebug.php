<?php
//Debug output path DEBUG_PATH is defined in settingsHydrogen.php
include_once ('settingsHydrogen.php');

if (!isset($settings['DEBUG_PATH'])) $settings['DEBUG_PATH'] = dirname(__FILE__) . "/debug.txt" ;
if (!isset($settings['DEBUG'])) $settings['DEBUG'] = false;
error_log ("DEBUG set to " . $settings['DEBUG'] . "; DEBUG_PATH=".$settings['DEBUG_PATH']);
function debug($info,$source=""){
	global $settings;
	global $debug;
	if (!isset($debug[$source])) $debug[$source]=true;
	if ($settings['DEBUG'] and $debug[$source]) {


		$output[0]=date("Y-m-d H:i:s");
		//'if' test is for command-line usage
		if (isset($_SERVER['REMOTE_ADDR'])) $output[1]=$_SERVER['REMOTE_ADDR']; else $output[1]="command line";
		if (isset($_SESSION['username'])) $output[2]=$_SESSION['username']; else $output[2]='unauthenticated';
		$output[3]=$info;
		if (isset($_SERVER['REQUEST_URI'])) $output[4]=$_SERVER['REQUEST_URI']; else $output[4]=$_SERVER['SCRIPT_FILENAME'];
		if ($source!="") $output[5]=$source;
		$fp = fopen($settings['DEBUG_PATH'], 'a');
		fwrite($fp, implode("\t | ",$output) . "\n");
		fclose($fp);
	}
}

?>