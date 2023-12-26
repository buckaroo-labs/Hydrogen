<?php
include_once ('settingsHydrogen.php');
include_once ('settingsPasswords.php');
require_once ('Hydrogen/clsDataSource.php');
// The default behavior is to disallow usernames 
//	which would match if forced to same case
if (!isset($caseSensitiveUsernames)) $caseSensitiveUsernames = false;
if (!isset($settings['DATAFILE_PATH'])) $settings['DATAFILE_PATH'] = dirname(__FILE__);

function lookUpUsername($username) {
	global $caseSensitiveUsernames;
	global $settings;
	$strResult="";
	$conn=new mysqli($settings['DEFAULT_DB_HOST'], $settings['DEFAULT_DB_USER'] , $settings['DEFAULT_DB_PASS'], $settings['DEFAULT_DB_INST']);
	$where=" upper(username)= upper(?)";
	if ($caseSensitiveUsernames) $where = " username=?";
	$sql = "select count(*)  as user_count , max(username) as uname from user where " . $where;
    $stmt=$conn->prepare($sql); 
	if ( false===$stmt )         die('prepare() failed: ' . htmlspecialchars($conn->error));
    $rc=$stmt->bind_param("s", $username) ;
	if ( false===$rc )         die('bind_param() failed: ' . htmlspecialchars($stmt->error));

	$stmt->execute();
	$stmt->bind_result($uCount, $uname);
	while ($stmt->fetch()) {
		if ($uCount==1) $strResult=$uname;
	}

	return $strResult;

}



function authenticate($uname, $password) {
	$success=0;
	global $settings;
	//if username is not clean, neutralize it
    $username=filter_var($uname,FILTER_SANITIZE_EMAIL);
	//if ($username!=$uname) $username=filter_var($uname,FILTER_validate_email);
	if (lookUpUsername($username) != '') {
		global $dds;
		global $caseSensitiveUsernames;
		$conn=new mysqli($settings['DEFAULT_DB_HOST'], $settings['DEFAULT_DB_USER'] , $settings['DEFAULT_DB_PASS'], $settings['DEFAULT_DB_INST']);
		$where=" upper(username)=upper(?)";
		if ($caseSensitiveUsernames) $where = " username=?";
		$sql = "select count(*) user_count, max(password_hash) max_hash from user where " . $where;
		$stmt=$conn->prepare($sql); 
		if ( false===$stmt )         die('prepare() failed: ' . htmlspecialchars($conn->error));
		$rc=$stmt->bind_param("s", $username); 
		if ( false===$rc )         die('bind_param() failed: ' . htmlspecialchars($stmt->error));
		$stmt->execute();
		$stmt->bind_result($uCount, $hash);
		while ($stmt->fetch()) {
			if ($success==0) {			
				if ($uCount > 0) {
					if (password_verify($password,$hash)) $success=1;
					$filepath=$settings['DATAFILE_PATH'] . '/user_login.log';
					$fp = fopen($filepath, 'a');
					fwrite($fp,  $username . ',' . $_SERVER['REMOTE_ADDR'] . ',' . date("D M j G:i:s T Y") . "\n");
					fclose($fp);

					$accessToken = password_hash($_SERVER['REMOTE_ADDR']. date("D M j G:i:s T Y"),PASSWORD_BCRYPT) ;
					$conn=new mysqli($settings['DEFAULT_DB_HOST'], $settings['DEFAULT_DB_USER'] , $settings['DEFAULT_DB_PASS'], $settings['DEFAULT_DB_INST']);
					$sql="update user set access_token='" . $accessToken . "', last_ip='" .  $_SERVER['REMOTE_ADDR'] . "' , last_login=CURRENT_TIMESTAMP where username='" . $uname . "'";
					$dds->setSQL($sql);

				} else {
					$_SESSION['errMsg']='Invalid username/password.';
					$filepath=$settings['DATAFILE_PATH'] . '/failed_login_attempts.log';
					$fp = fopen($filepath, 'a');
					fwrite($fp,  $username . ',' . $password . ',' . $_SERVER['REMOTE_ADDR'] . ',' . date("D M j G:i:s T Y") . "\n");
					fclose($fp);
				}
			}
		}
	}
return $success;
}

?>
