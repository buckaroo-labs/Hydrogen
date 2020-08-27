<?php
//This file and the "Hydrogen/elem*.php" includes are a PHP adapatation of the HTML/CSS template at
// https://www.w3schools.com/w3css/tryit.asp?filename=tryw3css_templates_webpage&stacked=h

//Include this file in your app's pages if you want
// a consistent look (menu bar, sidebar, etc.) from page to page

//EXAMPLE PAGE:
/*
<?php
$pagetitle="Home | MySite";
$headline = '<h1>My Site</h1><h3>My super awesome tagline</h3>' ;
include "Hydrogen/pgTemplate.php";
?>

<!-- Main content: shift it to the right by 250 pixels when the sidebar is visible -->
<div class="w3-main" style="margin-left:250px">

<?php include 'Hydrogen/elemLogoHeadline.php';  	 ?>

  <div class="w3-row w3-padding-64">
    <div class="w3-twothird w3-container">
      <h1 class="w3-text-teal">Heading</h1>
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Lorem ipsum
        dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
    </div>
    <div class="w3-third w3-container">
      <p class="w3-border w3-padding-large w3-padding-32 w3-center">AD</p>
      <p class="w3-border w3-padding-large w3-padding-64 w3-center">AD</p>
    </div>
  </div>

  <!-- Pagination
  <div class="w3-center w3-padding-32">
    <div class="w3-bar">
      <a class="w3-button w3-black" href="#">1</a>
      <a class="w3-button w3-hover-black" href="#">2</a>
      <a class="w3-button w3-hover-black" href="#">»</a>
    </div>
  </div>
   -->

<!-- END MAIN -->
</div>

<?php
	//Yes, it goes at the top, but it may use variables (session status) that are set by what happens in the middle -
	//   so include it at the end and then let it float to the top
	include 'Hydrogen/elemNavbar.php';
	include "Hydrogen/elemFooter.php";
?>
</body></html>
*/


if(!isset($_SESSION)) session_start();
include_once 'settingsHydrogen.php';
if(!isset( $settings['search_page'])) $settings['search_page']="/";
if(!isset( $settings['login_page'])) $settings['login_page'] = "/";

?>


<!DOCTYPE html>
<html lang="en">
<?php

if (isset($pagetitle)) {
echo "<title>" . $pagetitle . "</title>";
//to prevent unintentionally setting the same title on subsequent pages using this template
unset ($pagetitle);
}
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-black.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif;}
.w3-sidebar {
  z-index: 3;
  width: 250px;
  top: 55px;
  bottom: 0;
  height: inherit;
}
</style>
<script src="/scripts/sorttable.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<body>

<?php

	include 'Hydrogen/elemSidebar.php';
  // include 'Hydrogen/elemAnalytics.php';
  if (isset($settings['page_usage_tracking'])) {
    if ($settings['page_usage_tracking']==true)
    $pageUser="unauthenticated";
    if (isset($_SESSION['username'])) $pageUser=$_SESSION['username'];
      $sql="INSERT INTO OVERDRIVE.PAGE_USAGE (server,ip,remote_host,URI,username) VALUES ('". $_SERVER['SERVER_NAME']."','". $_SERVER['REMOTE_ADDR']."','". $_SERVER['REMOTE_HOST']."','". $_SERVER['REQUEST_URI']."','". $pageUser."')";
      $dds->setSQL ($sql);

  }

?>


