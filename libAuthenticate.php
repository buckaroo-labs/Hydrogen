<?php
require_once ('settingsHydrogen.php');
require_once ('settingsPasswords.php');
require_once ('Hydrogen/clsDataSource.php');
//The default behavior is to disallow usernames 
//	which would match if forced to same case
if (!isset($caseSensitiveUsernames)) $caseSensitiveUsernames = false;

function lookUpUsername($username) {
	global $dds;
	global $caseSensitiveUsernames;
	$where=" upper(username)='" . strtoupper($username) . "'";
	if ($caseSensitiveUsernames) $where = " username='" . $username . "'";
	$sql = "select count(*) from user where " . $where;
	$intResult = $dds->getInt($sql);
	if ($intResult > 0) {
		$sql = "select username from user where " . $where;
		$strResult = $dds->getString($sql);
	} else {
		$strResult="";
	}
	return $strResult;
}



function authenticate($username, $password) {
$success=0;

	//this is dumb but simple
/*
	if ($username=="test" and $password=="foo") {		
		$success=1;
		$filepath=DATAFILE_PATH . '/user_login.log';
		$fp = fopen($filepath, 'a');
		fwrite($fp,  $username . ',' . $_SERVER['REMOTE_ADDR'] . ',' . date("D M j G:i:s T Y") . "\n");
		fclose($fp);
	} else {
		$_SESSION['errMsg']='Invalid username/password.';
		$filepath=DATAFILE_PATH . '/failed_login_attempts.log';
		$fp = fopen($filepath, 'a');
		fwrite($fp,  $username . ',' . $password . ',' . $_SERVER['REMOTE_ADDR'] . ',' . date("D M j G:i:s T Y") . "\n");
		fclose($fp);
	}
*/	

	if (lookUpUsername($username) != '') {
		global $dds;
		global $caseSensitiveUsernames;
		$where=" upper(username)='" . strtoupper($username) . "'";
		if ($caseSensitiveUsernames) $where = " username='" . $username . "'";
		$sql = "select count(*) from user where " . $where;
		$where .= " and password='" . password_hash($password,PASSWORD_BCRYPT). "'";
		$intResult = $dds->getInt($sql);
		if ($intResult > 0) {
			$success=1;
			$filepath=DATAFILE_PATH . '/user_login.log';
			$fp = fopen($filepath, 'a');
			fwrite($fp,  $username . ',' . $_SERVER['REMOTE_ADDR'] . ',' . date("D M j G:i:s T Y") . "\n");
			fclose($fp);
		} 
	} else {
		$_SESSION['errMsg']='Invalid username/password.';
		$filepath=DATAFILE_PATH . '/failed_login_attempts.log';
		$fp = fopen($filepath, 'a');
		fwrite($fp,  $username . ',' . $password . ',' . $_SERVER['REMOTE_ADDR'] . ',' . date("D M j G:i:s T Y") . "\n");
		fclose($fp);
	}

return $success;
}

?>


