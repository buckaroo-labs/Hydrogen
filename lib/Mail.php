<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once("settingsHydrogen.php");
//Load Composer's autoloader (created by composer, not included with PHPMailer)
if (file_exists('vendor/autoload.php'))  {
	require 'vendor/autoload.php';
	$mail = new PHPMailer(true);
	//Server settings
	$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
	$mail->isSMTP();                                            //Send using SMTP
	$mail->Host       = $settings['SMTPHost'];                     //Set the SMTP server to send through
	$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
	$mail->Username   = $settings['SMTPUsername'];                     //SMTP username
	$mail->Password   = $settings['SMTPPassword'];                               //SMTP password
	$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
	$mail->Port       = $settings['SMTPPort'];                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

	//Create an instance; passing `true` enables exceptions
	function sendMail($mailbody,$mailsubject,$mailtoaddress,$mailfromaddress,$mailfromname='',$mailtoname='',$verbose=false) {
		global $mail;
		global $settings;
		try {

			//Recipients
			if (strlen($mailfromname>1)) {
				$mail->setFrom($mailfromaddress,$mailfromname);
			} else {
				$mail->setFrom($mailfromaddress);
			}
			//$mail->setFrom('from@example.com', 'Mailer');
			if (strlen($mailtoname>1)) {
				$mail->addAddress($mailtoaddress,$mailtoname);
			} else {
				$mail->addAddress($mailtoaddress);
			}
			//$mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
			//$mail->addAddress('ellen@example.com');               //Name is optional
			
			$mail->addReplyTo($mailfromaddress, 'Account services');
			//$mail->addCC('cc@example.com');
			//$mail->addBCC('bcc@example.com');

			//Attachments
			//$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
			//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			$mail->Subject = $mailsubject;
			$mail->Body = $mailbody;
			//$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
			//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
			if (!$verbose) ob_start();
			$mail->send();
			if (!$verbose) ob_end_clean();
			return true;
		} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			return false;
		}
	}
}
?>