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
	$include= "Hydrogen/pages/Login.php";  
		$pagetitle="Log In";
	$headline = '<h1>Log In</h1>' ;
} elseif (isset($_GET['p']) && strcmp($_GET['p'],'register')==0) {
	$include= "Hydrogen/pages/Register.php";  
		$pagetitle="Register";
	$headline = '<h1>Register</h1>' ;
} else {
	$include= "Hydrogen/pages/Sample.php";  
	$pagetitle="Demo";
	$headline = '<h1>Demo</h1>' ;
}

include "Hydrogen/pgTemplate.php";


?>
<!-- Main content: shift it to the right when the sidebar is visible -->
<div class="w3-main">

  <div class="w3-row w3-padding-64">
    <div class="w3-twothird w3-container">
		<?php include $include; ?>
	</div>
    <div class="w3-third w3-container">
  	</div>
  </div>

</div>
<?php
	//Yes, it goes at the top, but it may use variables (session status) that are set by what happens in the middle -
	//   so include it at the end and then let it float to the top
	include 'Hydrogen/elements/Navbar.php';
	include "Hydrogen/elements/Footer.php";
?>
</body></html>