<?php
include_once ('settingsHydrogen.php');
include_once ('settingsPasswords.php');
require_once ('Hydrogen/clsDataSource.php');
//The default behavior is to disallow usernames 
//	which would match if forced to same case
if (!isset($caseSensitiveUsernames)) $caseSensitiveUsernames = false;
if (!isset($settings['DATAFILE_PATH'])) $settings['DATAFILE_PATH'] = dirname(__FILE__);

function lookUpUsername($username) {
	global $dds;
	global $caseSensitiveUsernames;
	$where=" upper(username)='" . strtoupper($username) . "'";
	if ($caseSensitiveUsernames) $where = " username='" . $username . "'";
	$sql = "select count(*) from users where " . $where;
	$intResult = $dds->getInt($sql);
	if ($intResult > 0) {
		$sql = "select username from users where " . $where;
		$strResult = $dds->getString($sql);
	} else {
		$strResult="";
	}
	return $strResult;
}



function authenticate($username, $password) {
$success=0;
global $settings;

	if (lookUpUsername($username) != '') {
		global $dds;
		global $caseSensitiveUsernames;
		$where=" upper(username)='" . strtoupper($username) . "'";
		if ($caseSensitiveUsernames) $where = " username='" . $username . "'";
		$sql = "select count(*) from users where " . $where;
		$where .= " and password='" . password_hash($password,PASSWORD_BCRYPT). "'";
		$intResult = $dds->getInt($sql);
		if ($intResult > 0) {
			$success=1;
			$filepath=$settings['DATAFILE_PATH'] . '/user_login.log';
			$fp = fopen($filepath, 'a');
			fwrite($fp,  $username . ',' . $_SERVER['REMOTE_ADDR'] . ',' . date("D M j G:i:s T Y") . "\n");
			fclose($fp);
		} 
	} else {
		$_SESSION['errMsg']='Invalid username/password.';
		$filepath=$settings['DATAFILE_PATH'] . '/failed_login_attempts.log';
		$fp = fopen($filepath, 'a');
		fwrite($fp,  $username . ',' . $password . ',' . $_SERVER['REMOTE_ADDR'] . ',' . date("D M j G:i:s T Y") . "\n");
		fclose($fp);
	}

return $success;
}

?>


