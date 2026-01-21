<?php
require_once("Hydrogen/lib/Mail.php");
	$instructions="";
	$feedback="";
	if (!file_exists('vendor/autoload.php')) {
		 $cwdstr=getcwd();
		 $failed=true;
		 $feedback.='<h1>Check 1 of 3</h1><br>
    <ul class="setupChecklist">
    <li><span style="color:red">X</span> Composer installation</li>
    <li><span >?</span> PHPMailer installation</li>
    <li><span >?</span> SMTP configuration</li>
  </ul>
  <BR>The file <code>vendor/autoload.php</code> was not found in <code>' . 
		 	$cwdstr. '</code>. This is created when installing PHPMailer with 
			<a target="_blank" href="https://getcomposer.org/download/">Composer</a>.';
		 $fixit.='<BR><code>cd ' . $cwdstr . '</code><BR> <code>composer require phpmailer/phpmailer</code>';
	} elseif (!file_exists('vendor/phpmailer/phpmailer/README.md')) {
		 $cwdstr=getcwd();
		 $failed=true;
		 $feedback.='<h1>Check 2 of 3</h1><br>
    <ul class="setupChecklist">
    <li><span style="color:green">&#10003;</span> Composer installation</li>
    <li><span style="color:red">X</span> PHPMailer installation</li>
    <li><span >?</span> SMTP configuration</li>
  </ul><BR>The file <code>vendor/phpmailer/phpmailer/README.md</code> was not found in <code>' . 
		 	$cwdstr. '</code>. This is created when installing PHPMailer with 
			<a target="_blank" href="https://getcomposer.org/download/">Composer</a>.';
		 $fixit.='<BR><code>cd ' . $cwdstr . '</code><BR> <code>composer require phpmailer/phpmailer</code>';
	} elseif (file_exists('foo.md')) {
		 $cwdstr=getcwd();
		 $failed=true;
		 $feedback.='<BR>The dummy file <code>foo.md</code> was not found in <code>' . 
		 	$cwdstr. '</code>. This is checked to test walkthrough output for 
			<a target="_blank" href="https://getcomposer.org/download/">Composer</a>.';
		 $fixit.='<BR><code>cd ' . $cwdstr . '</code><BR> <code>composer require phpmailer/phpmailer</code>';
	} elseif (!array_key_exists('SMTPHost',$settings) || !array_key_exists('SMTPPort',$settings) || 
		!array_key_exists('SMTPUsername',$settings) || !array_key_exists('SMTPPassword',$settings) ||
		!array_key_exists('mailfromaddress',$settings)  ) {
		 $failed=true;
		 $feedback.='<h1>Check 3 of 3</h1><br>
    <ul class="setupChecklist">
    <li><span style="color:green">&#10003;</span> Composer installation</li>
    <li><span style="color:green">&#10003;</span> PHPMailer installation</li>
    <li><span style="color:red">X</span> SMTP configuration</li>
  </ul><BR>Some SMTP Configuration settings are missing from the configuration files.';
		 $instructions.="Set the following values in <code>settingsHydrogen.php</code>, substituting the appropriate values: <br><br>" .
		 '<code>$settings["mailfromaddress"]="noreply@example.com";</code><br>' .
		 '<code>$settings["SMTPHost"]="smtp.example.com";</code><br>' .
		 '<code>$settings["SMTPPort"]=465;</code><br>'.
		 '<code>$settings["SMTPUsername"]="noreply@example.com";</code><br><br>';
		 $instructions .= 'Set the following values in <code>settingsPasswords.php</code>, substituting the appropriate values: <br><br>' .
		 '<code>$settings["SMTPPassword"]="xxxxxxxxxxxxx";</code>';
	}
	if (strlen($feedback)>0) {
		echo '<p id="feedback">' . $feedback . '</p>';
		if (strlen($fixit)>0) {
			echo '<p>Please execute the following commands (if any) on your server and then refresh this page:</p><p id="instructions">' . $fixit . '</p>';
		} else if (strlen($instructions)>0) {
			echo '<p id="instructions">' . $instructions . '</p>';
		}
	} else {
		//All minimal setup tests have completed. Now we can check for POSTED
		//data, handle it, and display the results or a default response.

		$default_response='<h3>Mail Setup</h3><p>Mail setup appears to be complete.<p>';
		$default_response .= '<p>Your SMTP Server hostname is <code>' . 		$settings['SMTPHost'] . '</code>.<p>';
		$default_response .=  '<p>Your SMTP Server port is <code>' . $settings['SMTPPort'] . '</code>.<p>';
		$default_response .=  '<p>Your SMTP Server username is <code>' . $settings['SMTPUsername'] . '</code>.<p>';
		$default_response .=  '<p>Your SMTP Server password should be found in your <code>settingsPasswords.php</code> file.<p>';
		$default_response .=  '<div id="sendTestEmail"><form method="POST">
		  <label for="email">Send a test email to:</label>
  			<input type="email" id="email" name="email_test_destination">
  			<input type="submit" value="Send">
		</form></div>';

		if (isset($_POST['email_test_destination'])) {
			if (!filter_var($_POST['email_test_destination'],FILTER_VALIDATE_EMAIL)) {
				echo '<p>Invalid email input.</p>';
			} else {
				if (sendMail('This email is to test the configuration of your new application.','Application configuration',$_POST['email_test_destination'],$settings['mailfromaddress'],'','',true)) {
					echo '<div id="mailtestresults" style="margin-top 30px; font-weight: bold; color: red;"><p>Test email sent successfully.</p></div>'; //success;
				} else {
					echo '<div id="mailtestresults" style="margin-top 30px; font-weight: bold; color: red;"><p>Test email failed.</p></div>';//failure;
				}
			}

		} else {
			echo $default_response;
		}
	}

?>