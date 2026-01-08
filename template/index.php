<?php

/* The following code is meant to help you get started with configuration. 
You won't need to include it in your own application.
***CONFIG-START****/
//This file will load your app's key settings, and will in turn source the 
//  password settings, which are in a separate file not tracked in git.
//  if, when it is done, there is no value in $settings['JWT_SECRET_KEY'], then the browser
//  will be redirected to the setup page. Your application needs a unique secret key
//  to secure login tokens and sqlite database files.
require_once('settingsHydrogen.php');
if (empty($settings['JWT-SECRET-KEY'])) {
  header("Location: admin.php");
  exit;
}
/*****CONFIG-END *****/

if (isset($_GET['p']) && strcmp($_GET['p'],'login')==0) {
	include "Hydrogen/pages/Login.php";  
} elseif (isset($_GET['p']) && strcmp($_GET['p'],'register')==0) {
	include "Hydrogen/pages/Register.php";  
} else {
	include "Hydrogen/pages/Sample.php";  
}
	//Yes, it goes at the top, but it may use variables (session status) that are set by what happens in the middle -
	//   so include it at the end and then let it float to the top
	include 'Hydrogen/elements/Navbar.php';
	include "Hydrogen/elements/Footer.php";
?>
</body></html>