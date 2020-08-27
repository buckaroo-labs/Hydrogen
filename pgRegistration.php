<?php

// Define a registration page URL in your included settings file
// 		or page template; or use the default defined in settingsLogin.php.
// Your registration page may INCLUDE or REQUIRE Hydrogen/pgRegistration.php 
//		but should not BE this file, as doing that either in this code 
//		or in a hyperlink will put the user in the Hydrogen 
//    	subdirectory rather than the directory for your app 
// 		and then things will break due to relative references 
//		like the ones below


require_once ("Hydrogen/settingsLogin.php");
require_once ('Hydrogen/clsPasswdRules.php');
require_once ('Hydrogen/clsDataSource.php');
require_once ('Hydrogen/clsSQLBuilder.php');
require_once ('Hydrogen/libFilter.php');
require_once ('Hydrogen/libAuthenticate.php');

/* This page has FOUR sequential use cases:
1. No GET or POST variables. Ask for an email address
2. POSTed email address. Validate it and send an email with link to this page including a session-specific code as GET variable .
3. GET the code from case 2. If it is valid for the session, set the boolean $password_reset to true, and ask for a new password.
4. POSTed code and password. Update the password for the user.

*/

$username = sanitizeGetVar('username');
$password = sanitizePostVar('password');
$reset_code = sanitizeGetVar('reset_code');
$resetPostCode = sanitizePostVar('reset_code');
$resetPostUsername = sanitizePostVar('username');
if (filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) $email = $_POST['email'];

$useCase=1;
if (isset($email) and $email !="") $useCase=2;
if (isset($reset_code) and $reset_code !="") $useCase=3;
if (isset($password) and $password!="") $useCase=4;
debug ("Use Case: " . $useCase);


function validateMail ($email_address) {
	global $emailValid;
	global $dds;
	global $username;
	$emailValid=false;
	$sql="select count(*),max(username) from users where email='" . $email_address . "'";
	$result=$dds->setSQL($sql);
	$row=$dds->getNextRow();
	if ($row[0]=1) 	{
		$emailValid=true;
		$username=$row[1];
	}
	return $emailValid;
}


function validateResetCode ($code_value,$user_name) {
	global $dds;
	$validated=false;
	$sql = "select count(*) from users where username='" . $user_name . "' and reset_code='" . $code_value . "' and session_id='" . session_id() . "'";
	$result=$dds->setSQL($sql);
	$row=$dds->getNextRow();
	if ($row[0]=1) 	$validated=true;
	return $validated;
}

function sendMail($mailTo, $resetLink) {
	global $dds;
	$sql ="BEGIN
	EXECUTE IMMEDIATE 'ALTER SESSION SET smtp_out_server = ''mailrelay.nw1.nwestnetwork.com'''; 
	UTL_MAIL.send(sender => 'oss-db@ziply.com',
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
function sendMail_old ($mailTo, $resetLink) {
	//For Windows only
	ini_set('SMTP','mailrelay.nw1.nwestnetwork.com');
	ini_set('smtp_port',25);

	$to = $mailTo;
	$subject = "Password reset";
	
	$message = '
	<html>
	<head>
	<title>HTML email</title>
	</head>
	<body>
	<p>Click the link below to reset your password:</p>
	<a href="' . $resetLink . '">' . $resetLink . '</a>
	</body>
	</html>
	';
	
	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	
	// More headers
	$headers .= 'From: OSS Core DB Team <oss-db@ziply.com>' . "\r\n";
	$headers .= 'bcc: kent.heiner@ziply.com' . "\r\n";
	
	mail($to,$subject,$message,$headers);

}

function createResetCode ($email_address) {
	global $dds;
	global $username;

	$strKeyspace = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; 
	//generate random alphanumeric code, 25 char length
	$new_code = substr(str_shuffle($strKeyspace),0,25);

	//debug ("Creating reset code for " . $email_address . ", Session ID " . session_id() . ": " . $new_code);
	$sql = "update users set reset_code='" . $new_code . "', session_id='" . session_id() . "' where email='" . $email_address . "'";
	$dds->setSQL($sql);

	$reset_link= 'http://' . $_SERVER['SERVER_NAME'];
	if ($_SERVER['SERVER_PORT']=='443') $reset_link= 'https://' . $_SERVER['SERVER_NAME'];
	if ($_SERVER['SERVER_PORT']!='80') $reset_link.= ":" . $_SERVER['SERVER_PORT'] ;
	$reset_link.= $_SERVER['REQUEST_URI'] . "?username=" . $username . "&reset_code=" . $new_code;
	debug ("Reset link: " . $reset_link);
	sendMail($email_address,$reset_link);
}

if ($useCase==1 or $useCase==3) {

	//validate the code
	if($useCase==3 and validateResetCode($reset_code,$username)) $password_reset=true;
	if($useCase==3 and !validateResetCode($reset_code,$username)) debug ("Invalid reset code for use case 3");

	echo '<form action="' . $settings['registration_page'] . '" method="post" name="regForm" id="regForm" >
	<table>';

	if (!isset($password_reset)) {
		echo '<tr><td>Ziply e-mail (address@ziply.com): </td>';
		echo '<td><input name="email" type="text" id="usr_email"  maxlength="30" size="25" value="';
		if(isset($_POST["email"])) echo $email; 
		echo '"></td></tr>';
	}
	else {
		//Default password rule is 12-character minumum. 
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
		</tr>';}
		
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
		if (validateMail($email)) createResetCode($email);
		//send an email with link to this page including a session-specific code as GET variable
		
	}
	if($useCase==2 ) {
		if (!$emailValid) $registration_message="<h4>Invalid email address</h4>";

	}

	if($useCase==4 and validateResetCode($resetPostCode,$resetPostUsername)) $password_reset=true;
	//Default password rule is 12-character minumum. 
	if (!isset($passwordRules)) {
		$passwordRules=new PasswordRules(false);
		$passwordRules->addRule("min",12,"/\S/","character");
	}
	$rules = implode('<br>',$passwordRules->showRules());

	
	$registration_success = "<h4>Password reset successful.</h4>";


	if ($useCase==4 and $password_reset) {
			if ($passwordRules->checkPassword($password)) {

					//register the user (in the database) with hashed password
					// and delete the reset code
					$hash =password_hash($password,PASSWORD_BCRYPT);
					$sql="update users set session_id=null, reset_code=null, password='". $hash . "' where username='" . $username . "'";
					//$sql="update users set password='". $hash . "' where username='" . $resetPostUsername . "'";
					$dds->setSQL($sql);
					$registration_message = $registration_success;
			} else {
				$registration_message ="<h4>Password invalid. " . $rules . "</h4> " . $rules ;
			}
	} else {
		if ($useCase==4 or $emailValid) $registration_message ="<p>Check your inbox for a password reset link. Keep this browser window open and open the link using this browser (OK to click on it if this browser is your default).</p>";
	}
echo $registration_message;
debug ($registration_message);
}

?>