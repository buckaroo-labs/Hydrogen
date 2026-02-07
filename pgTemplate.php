<?php
ob_start();


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

<div class="w3-main">

<?php include 'Hydrogen/elements/elemLogoHeadline.php';  	 ?>

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
	include 'Hydrogen/elements/Navbar.php';
	include "Hydrogen/elements/Footer.php";
?>
</body></html>
*/

function showUsernameAndLogoutButton() {
	global $settings;
	//Nah.
	/*
	echo ('<div class="w3-main"><table name="successOK"><tr><td>Logged in as </td><td class="username">' . $_SESSION['username'] .
	 '</td></tr></table>');
	echo ('	<form class="access" id="logout" action="' . $settings['login_page'] . '" method="post">');
	echo ('	<input type="hidden" name="flow" value="logOut">');
	echo ('	<input type="submit" value="Log out">');
	echo ('	</form></div>');
	*/
}


$layout='default';
if(isset($_POST['layout']) && $_POST['layout']=='iframe') {
    $layout='iframe';
}
if(isset($_GET['layout']) && $_GET['layout']=='iframe') {
    $layout='iframe';
}

if(!isset($_SESSION)) session_start();
require_once 'settingsHydrogen.php';
if (!isset($_SESSION['setup_mode']) ) require_once 'Hydrogen/lib/Authenticate.php';
if (isset($_SESSION['setup_mode']) ) require_once ('Hydrogen/lib/Debug.php');
require_once('Hydrogen/lib/State.php');
if(!isset( $settings['search_page'])) $settings['search_page']="/";
if(!isset( $settings['login_page'])) $settings['login_page'] = "/";


if (!isset($_SESSION['username']) && isset($_COOKIE[$settings['JWTTokenName']])) {

  if ($tokenUser=validateJWT($_COOKIE[$settings['JWTTokenName']])) {
    debug ("Validated JWT token for " . $tokenUser);
    $_SESSION['username'] = $tokenUser;
  }

}

//Handle LogOut
function logOut() {
	global $settings;
	//clear the session variables to log them out
	$_SESSION=array();

	//2025-12-08
	//remove the persistent login cookie data and expire it
	setcookie($settings['JWTTokenName'], "", time() - 3600);

}

if (isset($_POST['flow'])) {
	if ($_POST['flow']="logOut"){
      debug ("Logging out");
			logOut();
	}
}

if (!isset($SESSION['username'])) {
	//Handle LogIn
	if (isset($_POST['uname']) and isset($_POST['passwd'])) {
		debug("Username and password posted","pgTemplate");
		$username=sanitizePostVar('uname'); //$_POST['uname']
		$password=sanitizePostVar('passwd'); ; //$_POST['passwd']
		//the credentials are there, so attempt to authenticate
		//using whatever method is defined in lib/Authenticate.php
		if (authenticate($username,$password)==1) {
			$_SESSION['username']=$_POST['uname'];
			//the user is now logged in
			$_SESSION['password']=$_POST['passwd'];
			unset($_SESSION['errMsg']);
			$done_authenticating=true;
			debug("Successful authentication","pgTemplate");
		} else {
			debug("Unsuccessful authentication","pgTemplate");
		}

	} else {$_POST['uname']="";  //define the variable so we can populate the form with it regardless of whether it was blank
	} // end IF (post:username)
}
//Now instead of the authenticate() function we will just
//use the 'username' token to check login status
if (isset($_SESSION['username'])) {

	showUsernameAndLogoutButton() ;
	$done_authenticating=true;
} // end IF (authenticated)

//before starting page output, we need to give whatever page is including this file a way
// to require users to be logged in 
if (isset($settings['login_page']) && isset($require_login) && !isset($_SESSION['username'])) {
	    header("Location: " . $settings['login_page']);
        die();
}

debug ("Template output begins");
ob_end_flush();
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="Hydrogen/style.css">
<link rel="stylesheet" href="styles.css">

<?php 
if (isset($settings['head_content'])) echo $settings['head_content'];
?>
<script src="Hydrogen/sorttable.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
	$(document).ready(function(){
		$('input[name="botkiller"]').val(1337);
		$("#myAccountImage").click(function(){
			$("#toggleAccountInfo").slideToggle();
			$("#toggleLoginInfo").slideToggle();
		});
	});
</script>
<body class="bg-light">

<?php

	if (!isset($settings['SIDEBAR_DISPLAY']) ) $settings['SIDEBAR_DISPLAY']=1;
  if(isset($_GET['layout']) && $_GET['layout']=='iframe') {
    $layout='iframe';
  }
  if ( $settings['SIDEBAR_DISPLAY']==1 && $layout !='iframe') {
    include 'Hydrogen/elements/Sidebar.php';
  }
  
  if (isset($settings['page_usage_tracking'])) {
    if ($settings['page_usage_tracking']==true)
    $pageUser="unauthenticated";
    if (isset($_SESSION['username'])) $pageUser=$_SESSION['username'];
      $sql="INSERT INTO PAGE_USAGE (server,ip,remote_host,URI,username) VALUES ('". $_SERVER['SERVER_NAME']."','". $_SERVER['REMOTE_ADDR']."','". $_SERVER['REMOTE_HOST']."','". $_SERVER['REQUEST_URI']."','". $pageUser."')";
      //$dds->setSQL ($sql);

  }

