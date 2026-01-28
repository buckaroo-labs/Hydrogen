<?php
include_once ('settingsHydrogen.php');
include_once ('settingsPasswords.php');
require_once ('Hydrogen/db/clsDataSource.php');
// The default behavior is to disallow usernames 
//	which would match if forced to same case
if (!isset($caseSensitiveUsernames)) $caseSensitiveUsernames = false;
if (!isset($settings['DATAFILE_PATH'])) $settings['DATAFILE_PATH'] = str_replace(dirname(__FILE__),'lib','log');

function generateJWT($userId) {
		global $settings;
        $header = base64_encode(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]));
        
        //2-week expiry
        $payload = base64_encode(json_encode([
            'uid' => $userId,
            'exp' => time() + $settings['JWTExpireTime']
        ]));

        $signature = hash_hmac('sha256', "$header.$payload", $settings['JWT_SECRET_KEY']);
        return "$header.$payload.$signature";
    }

function validateJWT($token) {
		global $settings;
        list($header, $payload, $signature) = explode('.', $token);
        $expected = hash_hmac('sha256', "$header.$payload", $settings['JWT_SECRET_KEY']);

        if (!hash_equals($expected, $signature)) return false;

        $data = json_decode(base64_decode($payload), true);
        if ($data['exp'] < time()) return false;

        return $data['uid'];
    }

function setPersistentLoginCookie($username) {
	global $settings;
	$JWT=generateJWT($username);
	$cookiestatus=setcookie($settings['JWTTokenName'], $JWT, [
		'expires' => time() + $settings['JWTExpireTime'], 
		'path' => '/',
		'domain' =>  $_SERVER['HTTP_HOST'],
		'secure' => true, // Use true if using HTTPS
		'httponly' => true, // Prevent JavaScript access
		'samesite' => 'Strict' // Adjust as needed
	]);
	$statusMsg="set JWT token at " . time() . " expiring " .  
		(time() + $settings['JWTExpireTime']) . " with name '" . $settings['JWTTokenName']
		 . "' for '" . $username . "' at '" . $_SERVER['HTTP_HOST'] ."'" ;
	if ($cookiestatus) debug ("SUCCESS: " . $statusMsg );
	if (!$cookiestatus) {
		debug ("FAILURE: " . $statusMsg );
		if (headers_sent()) debug ("Headers already sent" );
	}
}

function lookUpUsername($username) {
	global $caseSensitiveUsernames;
	global $settings;
	global $dds;
	$strResult="";
	$rows=0;
 	$where=" where upper(username)= upper('" . $username . "')";
	if ($caseSensitiveUsernames) $where = " where username='" . $username . "'";
	//$where="";
	$sql = "select count(*)  as user_count , max(username) as uname from user " . $where;
	debug("Querying db for username","lib/Authenticate");
    $result=$dds->setSQL($sql); 
	debug("Queried db for username","lib/Authenticate");
	while ($rrow=$dds->getNextRow("assoc")) {
		$rows++;
		debug("Checking db result for username","lib/Authenticate");
		if ($rrow['user_count']==1) {
			$strResult=$rrow['uname'];
			debug("Username found","lib/Authenticate");
		} else {
			error_log("Failed login attempt for unknown user: '" . $username . "'");
			debug("No matching username","lib/Authenticate");
		}
	}
	if ($rows==0) {
		debug("No username match: " . $username,"lib/Authenticate");
		error_log("Failed login attempt for unknown user '" . $username . "'");
	}
	return $strResult;

}

function authenticate($uname, $password) {
	//Change log:
	/*
	2025-12-08 Set a cookie upon successful authentication
	2026-01-25 repurpose the access_token field in user table

	*/
	$success=0;
	global $settings;
	//if username is not clean, neutralize it
    $username=filter_var($uname,FILTER_SANITIZE_EMAIL);
	//if ($username!=$uname) $username=filter_var($uname,FILTER_validate_email);
	debug("Looking up username","lib/Authenticate");
	if (lookUpUsername($username) != '') {
		debug("Username lookup successful","lib/Authenticate");
		global $dds;
		global $caseSensitiveUsernames;
		//$conn=new mysqli($settings['DEFAULT_DB_HOST'], $settings['DEFAULT_DB_USER'] , $settings['DEFAULT_DB_PASS'], $settings['DEFAULT_DB_INST']);
		debug("DB Type:" . $dds->dbType());
		if (strcmp($dds->dbType(),'mysql')==0) {
			$where=" upper(username)=upper(?)";
			if ($caseSensitiveUsernames) $where = " username=?";	
		} elseif (strcmp($dds->dbType(),'sqlite3')==0) {
			$where=" upper(username)=upper(:uname)";
			if ($caseSensitiveUsernames) $where = " username=:uname";	
		} else {
			//This library used to be Oracle compatible, 
			//but here is where I no longer bother to test. Beware.
			$where=" upper(username)=upper(':uname')";
			if ($caseSensitiveUsernames) $where = " username=':uname'";
		}
		$sql = "select count(*) user_count, max(password_hash) max_hash, max(access_token) as temp_pwd from user where " . $where;
		debug ("Querying db with SQL:" . $sql		);
		$stmt=$dds->prepare($sql); 
		if ( false===$stmt ) die('prepare() failed: ' . htmlspecialchars($dds->error));

		//Ideally, more of this binding would be handled at the class level, 
		//but each library requires a much different syntax.
		if (strcmp($dds->dbType(),'mysql')==0) {
			$rc=$stmt->bind_param("s", $username); 
			if ( false===$rc ) die('bind_param() failed: ' . htmlspecialchars($stmt->error));
		} elseif (strcmp($dds->dbType(),'sqlite3')==0) {
			$stmt->bindValue(':uname', $username, SQLITE3_TEXT);
		} else {
			//This repo used to be Oracle compatible, 
			//but here is where I no longer bother to test. Beware.
			oci_bind_by_name($stmt, ':uname', $username);
		}

		$result = $dds->getStmtResult($stmt);
		debug("Verifying password","lib/Authenticate");
		
		if (strcmp($dds->dbType(),'mysql')==0) {
			$rrow=$result->fetch_assoc();
		} elseif (strcmp($dds->dbType(),'sqlite3')==0) {
			$rrow = $result->fetchArray();
		} else {
			//This repo used to be Oracle compatible, 
			//but here is where I no longer bother. Beware.
			debug("Unsupported fetch from Oracle prepared statement.");
		}
	
		if ($rrow) {
			if (is_null( $rrow['max_hash'] )) debug("Hash is null.");
			debug("Checking hash: " . $rrow['max_hash'] . "(" . strlen($rrow['max_hash']) . ") chars","lib/Authenticate/141");
			if ( ($rrow['user_count'] > 0) && password_verify($password,$rrow['max_hash'])   ) {
					$success=1;
					debug("Successful authentication","lib/Authenticate");
					//deprecated:
					//$accessToken = password_hash($_SERVER['REMOTE_ADDR']. date("D M j G:i:s T Y"),PASSWORD_BCRYPT) ;
					//$conn=new mysqli($settings['DEFAULT_DB_HOST'], $settings['DEFAULT_DB_USER'] , $settings['DEFAULT_DB_PASS'], $settings['DEFAULT_DB_INST']);
					//$sql="update user set access_token='" . $accessToken . "', last_ip='" .  $_SERVER['REMOTE_ADDR'] . "' , last_login=CURRENT_TIMESTAMP where username='" . $uname . "'";
					//$dds->setSQL($sql);

					//write to file
					$filepath=$settings['DATAFILE_PATH'] . '/user_login.log';
					$fp = fopen($filepath, 'a');
					fwrite($fp,  $username . ',' . $_SERVER['REMOTE_ADDR'] . ',' . date("D M j G:i:s T Y") . "\n");
					fclose($fp);

					//set a persistent login cookie
					setPersistentLoginCookie($username) ;
			//adding a check for the temp password mailed for registration and resets
			} elseif (($rrow['user_count'] > 0) && password_verify($password,$rrow['temp_pwd'])) {
					$success=1;
					debug("Successful authentication with temp password","lib/Authenticate");
					setPersistentLoginCookie($username) ;
					$filepath=$settings['DATAFILE_PATH'] . '/user_login.log';
					$fp = fopen($filepath, 'a');
					fwrite($fp,  $username . ',' . $_SERVER['REMOTE_ADDR'] . ',' . date("D M j G:i:s T Y") . "\n");
					fclose($fp);
			} else {
				$errmsg='Invalid username/password.';
				$_SESSION['errMsg']=$errmsg;
				$errmsg .= ": " . $password;
				if (empty($rrow['user_count'])) $errmsg .= " (user not found)";
				error_log ($errmsg);
				debug($errmsg,"lib/Authenticate");
				//write to file
				$filepath=$settings['DATAFILE_PATH'] . '/failed_login_attempts.log';
				$fp = fopen($filepath, 'a');
				fwrite($fp,  $username . ',' . $password . ',' . $_SERVER['REMOTE_ADDR'] . ',' . date("D M j G:i:s T Y") . "\n");
				fclose($fp);
			}

		}
	}
	return $success;
}
