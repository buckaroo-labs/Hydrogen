<?php 

// This is a template to include in your app's pages if you want
// a consistent look (menu bar, sidebar, etc.) from page to page

//EXAMPLE PAGE:
/*
<?php 
$pagetitle="Home | MySite";
$headline = '<h1>My Site</h1><h3>My super awesome tagline</h3>' ;
include "Hydrogen/pgTemplate.php";
?>
<div id="main"><P>This is the home page for my new site.</P></div>
<?php include "Hydrogen/elemFooter.php"; ?>
</body></html>
*/
   
if(!isset($_SESSION)) session_start(); 
require_once 'settings.php';
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="Hydrogen/style.css">
<meta http-equiv="CONTENT-TYPE" content="text/html; charset=windows-1252">
<?php  if (isset($pagetitle)) {
echo "<title>" . $pagetitle . "</title>";
//to prevent unintentionally setting the same title on subsequent pages using this template
unset ($pagetitle);
}
?>
</head>
<body>

<?php

//Any of these elements can be switched off at the template level

// include 'Hydrogen/elemAnalytics.php';  
include 'Hydrogen/elemMenubar.php';  
include 'Hydrogen/elemLogoHeadline.php';  
include 'Hydrogen/elemSidebar.php'; 

//This template does NOT include the footer template (elemFooter.php) 
//or ending </BODY> or </HTML> tags.
//These should appear AFTER the individual page content.

?>



