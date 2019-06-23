<?php
//Debug output path DEBUG_PATH is defined in settings.php
include_once ('settingsHydrogen.php');

if (!isset($settings['DEBUG_PATH'])) $settings['DEBUG_PATH'] = dirname(__FILE__) . "/debug.txt" ;
if (!isset($settings['DEBUG'])) $settings['DEBUG'] = false;

function debug($info){
	global $settings;
	if ($settings['DEBUG']) {

		$output[0]=date("Y-m-d H:i:s");
		if (isset($_SERVER['REMOTE_ADDR'])) $output[1]=$_SERVER['REMOTE_ADDR']; else $output[1]="command line";
		if (isset($_SESSION['username'])) $output[2]=$_SESSION['username']; else $output[2]='null';
		$output[3]=$info;
		if (isset($_SERVER['REQUEST_URI'])) $output[4]=$_SERVER['REQUEST_URI']; else $output[4]=$_SERVER['SCRIPT_FILENAME'];
		$fp = fopen($settings['DEBUG_PATH'], 'a');
		fwrite($fp, implode("\t | ",$output) . "\n");
		fclose($fp);
	}
}

?>
