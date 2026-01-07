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
  header("Location: setup.php");
  exit;
}
/*****CONFIG-END *****/

$pagetitle="Page title";
$headline = '<h1>My Site</h1><h3>My super awesome tagline</h3>' ;
include "Hydrogen/pgTemplate.php";
?>

<!-- Main content: shift it to the right when the sidebar is visible -->
<div class="w3-main">

<?php include 'Hydrogen/elemLogoHeadline.php';  	 ?>

  <div class="w3-row w3-padding-64">
    <div class="w3-twothird w3-container">
      <h1 class="w3-text-teal">Heading</h1>
      <p>
		This example page shows how your page might look using the Hydrogen framework defaults. If it looks sort of plain, that means that there is less for you to undo when you customize your application.  Try out various screen widths and see how the layout changes.
	  </p>
    </div>
    <div class="w3-third w3-container">
      <p class="w3-border w3-padding-large w3-padding-32 w3-center">
		Locally installed <a target="_blank" href="Hydrogen/docs/index.html">Documentation</a>
	  </p>
      <p class="w3-border w3-padding-large w3-padding-64 w3-center">
		<a target="_blank" href="https://github.com/buckaroo-labs/Hydrogen">GitHub</a>
	  </p>
    </div>
  </div>

<!-- END MAIN -->
</div>

<?php
	//Yes, it goes at the top, but it may use variables (session status) that are set by what happens in the middle -
	//   so include it at the end and then let it float to the top
	include 'Hydrogen/elemNavbar.php';
	include "Hydrogen/elemFooter.php";
?>
</body></html>