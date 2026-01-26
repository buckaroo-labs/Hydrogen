<?php

// Define a registration page URL in your included settings file
// 		or page template; or use the default defined in settingsLogin.php.
// Your registration page may INCLUDE or REQUIRE Hydrogen/pages/Registration.php 
//		but should not BE this file, as doing that either in this code 
//		or in a hyperlink will put the user in the Hydrogen 
//    	subdirectory rather than the directory for your app 
// 		and then things will break due to relative references 
//		like the ones below

require_once ("Hydrogen/settingsLogin.php");
require_once ('Hydrogen/clsPasswdRules.php');
require_once ('Hydrogen/db/clsDataSource.php');
require_once ('Hydrogen/db/clsSQLBuilder.php');
require_once ('Hydrogen/lib/State.php');
require_once ('Hydrogen/lib/Mail.php');
require_once ('Hydrogen/lib/Authenticate.php');

/* This page has FOUR sequential use cases:
1. No GET or POST variables. Ask for an email address.



2. POSTed email address. 
	Old response: Validate it and send an email with link to this page 
		including a session-specific code as GET variable .
	New response: Emailed links are often censored and may not arrive. 
		Send a temporary password instead. Hash and store it like a normal
		password. Instruct the user to click a/the link to the login page
		and use the temporary username shown on-screen and their emailed password with it.

3. (deprecated) GET the code from case 2. If it is valid for the session, set the boolean $password_reset to true, 
and ask for a new password.

4. (deprecated) POSTed code and password. Update the password for the user.

*/

$username = sanitizeGetVar('username');
//$password = sanitizePostVar('password');
//$reset_code = sanitizeGetVar('reset_code');
//$resetPostCode = sanitizePostVar('reset_code');
//$resetPostUsername = sanitizePostVar('username');
if (isset($_POST['email']) && filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
	//bot prevention
	if (isset($_POST['botkiller']) && $_POST['botkiller']==1337) {
		$email = $_POST['email'];
	} else {
		debug ("Bot prevention has screened out a registration request.","pages/Register/38");
	}
}

$useCase=1;
if (isset($email) and $email !="") $useCase=2;
//if (isset($reset_code) and $reset_code !="") $useCase=3;
//if (isset($password) and $password!="") $useCase=4;
debug ("Use Case: " . $useCase,"pages/Register:46");


function validateMail ($email_address) {
	global $settings;
	global $dds;
	global $username;
	$emailValid=false;
	debug ("Validating email: " . $email_address, "pages/Register/54");
	$sql="select count(*) ucount, max(username) max_name from user where email='" . $email_address . "'";
	/** Using prepared statements was a good idea, but input has been sanitized and
	 * we need the same code to work for different DB libraries. Come back to this later.
	 */
	//$conn=new mysqli($settings['DEFAULT_DB_HOST'], $settings['DEFAULT_DB_USER'] , $settings['DEFAULT_DB_PASS'], $settings['DEFAULT_DB_INST']);
	//$stmt=$conn->prepare($sql); 
	//if ( false===$stmt )         die('prepare() failed: ' . htmlspecialchars($conn->error));
	//$rc=$stmt->bind_param("s", $email_address);  
	//if ( false===$rc )         die('bind_param() failed: ' . htmlspecialchars($stmt->error));
	//$stmt->execute();
	//$stmt->bind_result($uCount, $uname);
	$result=$dds->setSQL($sql);
	while ($rrow=$dds->getNextRow("assoc")) {
		debug ("Checking db result: " . $email_address, "pages/Register/68");
		if ($rrow['ucount']==1) {
			$emailValid=true;
			$username=$rrow['max_name'];
			debug ("DB result found: " . $email_address, "pages/Register/72");
		}
	}
	if (!$emailValid) {
		$sql = "INSERT INTO user (username,email) values ('" . $email_address . "','" . $email_address . "')";
		//$stmt=$conn->prepare($sql); 
		//if ( false===$stmt )         die('prepare() failed: ' . htmlspecialchars($conn->error));
		//$rc=$stmt->bind_param("ss", $email_address, $email_address); 
		//if ( false===$rc )         die('bind_param() failed: ' . htmlspecialchars($stmt->error));
		//$stmt->execute();
		debug ("Inserting email: " . $email_address, "pages/Register/82");
		$success=$dds->setSQL($sql);
		
		if ($success) {
			$emailValid=true;
			$username=$email_address;
			debug ("Inserted email: " . $email_address, "pages/Register/87");
		} else {
			debug ("email insertion failed: " . $email_address . ": " . $dds->getError() , "pages/Register/87");
		}
	}
	$returnStatus='true';
	if(!$emailValid) $returnStatus='false';
	debug ("Returning email validation of '" . $returnStatus . "': " . $email_address, "pages/Register/92");
	return $emailValid;
}

/*
function validateResetCode ($code_value,$user_name) {
	global $dds;
	$validated=false;
	$sql = "select count(*) from user where username='" . $user_name .
	 "' and reset_code='" . $code_value . "' and session_id='" . session_id() . "'";
	$result=$dds->setSQL($sql);
	$row=$dds->getNextRow();
	if ($row[0]=1) 	$validated=true;
	return $validated;
}

function sendMail_oracle($mailTo, $resetLink) {
	global $dds;
	$sql ="BEGIN
	EXECUTE IMMEDIATE 'ALTER SESSION SET smtp_out_server = ''mailrelay.foo.com'''; 
	UTL_MAIL.send(sender => 'compass@foo.com',
	recipients => '". $mailTo . "',
	subject => 'Password reset',
	message => '<html>
		<head>
		<title>HTML email</title>
		</head>
		<body>
		<p>A password reset has been requested for your ID in the application having the link below. If you did not make this request, the request may have been made in error.</p>
		<p>Click the link below to reset your password:</p><br>
		<a href=' || chr(34) || '". $resetLink . "' || chr(34) || '>' || '". $resetLink . "' || '</a>
		</body>
		</html>
		',
	mime_type => 'text/html; charset=UTF-8');
	END;";
	$dds->setSQL($sql);
}
*/
function sendResetMail($mailTo, $password, $username) {
	global $settings;
	$subject = "Registration or Password reset";
	
	$message = '
	<html>
	<head>
	<title>Account request</title>
	</head>
	<body>
	<p>';
	$message.='We have recieved a request to establish an account or reset a password. If you did not make this request, 
	you can ignore this email.';
	$message .='</p><p>Your username is "' . $username . '" and your temporary password is: </p>'
	 . $password . '<p>You will be able to reset your password after logging in with the temporary password.
	 We would like to provide you with a link to the login page in this message, 
	but we find that some systems refuse to deliver mail containing such links. Please follow the 
	directions you were given at the time this request was made.<p>
	</body>
	</html>	';
	$mailfromname="Account Service";
	if (isset($settings['mailfromname'])) $mailfromname=$settings['mailfromname'];
	sendMail($message,$subject,$mailTo,$settings['mailfromaddress'],$mailfromname,'Subscriber',false);

}
function sendResetMail_old ($mailTo, $resetLink) {
	global $settings;
	$subject = "Registration or Password reset";
	
	$message = '
	<html>
	<head>
	<title>Account request</title>
	</head>
	<body>
	<p>';
	$message='We have recieved a request to establish an account or reset a password. If you did not make this request, 
	you can ignore this email.';
	$remainder='</p><p>To set or reset your password, click the following link:</p>
	<a href="' . $resetLink . '">Reset password</a>
	</body>
	</html>	';
	//uncomment this when delivery troubleshooting is done
	//$message .= $remainder;
	$message.=' To set or reset your password, copy the following and paste it in your browser: ' 
	. str_replace('http://','',$resetLink) ;
	

	sendMail($message,$subject,$mailTo,$settings['mailfromaddress'],'Sheldrake Industries','Subscriber',true);

}

function createResetCode ($email_address) {
	global $dds;
	global $username;

	$strKeyspace = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; 
	//generate random alphanumeric code, 25 char length
	//$new_code = substr(str_shuffle($strKeyspace),0,25);
	//the above is not secure enough.
	$new_code='';
	for ($i = 0; $i < 25; $i++) {
        $randomIndex = random_int(0, strlen($strKeyspace)-1);
        $new_code .= $strKeyspace[$randomIndex];
    }

	//debug ("Creating temp password for " . $email_address . ", Session ID " . session_id() . ": " . $new_code);
	$sql = "update user set access_token='" . password_hash($new_code,PASSWORD_BCRYPT) . "', session_id='" . session_id() . 
		"' where email='" . $email_address . "'";
	$dds->setSQL($sql);
	/*
	$reset_link= 'http://' . $_SERVER['SERVER_NAME'];
	if ($_SERVER['SERVER_PORT']=='443') $reset_link= 'https://' . $_SERVER['SERVER_NAME'];
	if ($_SERVER['SERVER_PORT']!='80') $reset_link.= ":" . $_SERVER['SERVER_PORT'] ;
	$reset_link.= $_SERVER['REQUEST_URI'] . "?username=" . $username . "&reset_code=" . $new_code;
	debug ("Reset link: " . $reset_link);
	*/
	if (extension_loaded('oci8')) {
		//sendMail_oracle($email_address,$reset_link);
	} else {
		sendResetMail($email_address,$new_code,$username);
	}
}

if ($useCase==1 or $useCase==3) {
	debug ("Validating reset code","pages/Register:179");
	//validate the code
	if($useCase==3) {
		/*
		$result=validateResetCode($reset_code,$username);
		if ($result) {
			$password_reset=true;
			debug ("Valid reset code for use case 3");
		} else {
			 debug ("Invalid reset code for use case 3");
		}
		*/
	}

	echo '<form id="registrationForm" action="' . $settings['registration_page'] . '" method="post" name="regForm" id="regForm" >
	<table>';

	if (!isset($password_reset)) {
		echo '<tr><td>Your e-mail address: </td>';
		echo '<td>
		<input id="botkiller-input" type="hidden" name="botkiller" value="86">
		<input name="email" type="email" size="30" id="usr_email"  maxlength="50" size="25" value="';
		if(isset($_POST["email"])) echo $email; 
		echo '"></td></tr>';
	}
	else {
		//Default password rule is 12-character minumum. 
		/*
		if (!isset($passwordRules)) {
			$passwordRules=new PasswordRules(false);
			$passwordRules->addRule("min",12,"/\S/","character");
		}
		$rules = implode('<br>',$passwordRules->showRules());
		//echo "<h4>". $rules . "</h4>";
			echo '
		<tr>
		<td>New Password: </td>
		<td>
		<input name="password" type="password" id="pwd"  maxlength="30" size="25">
		<input type="hidden" id="reset_code" name="reset_code" value="'. $reset_code . '">
		<input type="hidden" id="username" name="username" value="'. $username . '">
		</td>
		</tr>
		<tr>
		<td>Re-enter password: </td>
		<td><input name="password2"  id="pwd2" type="password"  maxlength="30" size="25" equalto="#password"></td>
		</tr>
		<tr>
		<td colspan="2">&nbsp;</td>
		</tr>';
		*/
	}
		
	echo '
	</table>
	<p align="center">
	
	<input name="btnSubmit" type="submit" id="Register" value="Submit">
	</p>
	</form>';
} //use cases 1 and 3

if ($useCase==2 or $useCase==4) {
	$registration_message="";
	$password_reset=false;
	$emailValid=false;

	if($useCase==2 ) {
		if (validateMail($email)) {
			$emailValid=true;
			debug ("Valid email: " . $email, "pages/Register/273");
			//set and send a temporary password
			createResetCode($email);
		} else {
			debug ("Invalid email: " . $email, "pages/Register/276");
		}
		if (!$emailValid) $registration_message="<h4>Invalid email address</h4>";	
	}

	//deprecated
	//if($useCase==4 and validateResetCode($resetPostCode,$resetPostUsername)) $password_reset=true;
	//Default password rule is 12-character minumum. 
	if (!isset($passwordRules)) {
		$passwordRules=new PasswordRules(false);
		$passwordRules->addRule("min",12,"/\S/","character");
	}
	$rules = implode('<br>',$passwordRules->showRules());

	
	$registration_success = "<h4>Password reset successful.</h4>";

	//deprecated
	if ($useCase==4 and $password_reset) {
			if ($passwordRules->checkPassword($password)) {

					//register the user (in the database) with hashed password
					// and delete the reset code
					$hash =password_hash($password,PASSWORD_BCRYPT);
					$sql="update user set session_id=null, reset_code=null, password='". $hash .
						 "' where username='" . $username . "'";
					//$sql="update user set password='". $hash . "' where username='" . $resetPostUsername . "'";
					$dds->setSQL($sql);
					$registration_message = $registration_success;
			} else {
				$registration_message ="<h4>Password invalid. " . $rules . "</h4> " . $rules ;
			}
	} else {
		if ($useCase==4 or $emailValid) $registration_message ="<p>Check your inbox for a 
		temporary password. Click the 'Log in' link above, keep this browser window open,
		 and enter the username and password that is mailed to you.</p>";
	}
echo $registration_message;
debug ($registration_message);
}

?>