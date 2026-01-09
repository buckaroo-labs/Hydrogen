<?php
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
		//good to go!
		echo '<h3>Mail Setup</h3><p>Mail setup appears to be complete.<p>';
		//give config details ...
		echo '<p>Your SMTP Server hostname is <code>' . $settings['SMTPHost'] . '</code>.<p>';
		echo '<p>Your SMTP Server port is <code>' . $settings['SMTPPort'] . '</code>.<p>';
		echo '<p>Your SMTP Server username is <code>' . $settings['SMTPUsername'] . '</code>.<p>';
		echo '<p>Your SMTP Server password should be found in your <code>settingsPasswords.php</code> file.<p>';
	}

?>