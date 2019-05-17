
<?php
/*****************************************************

EXAMPLE USAGE:
<?php 
$pagetitle="Log In | MySite";
include "Hydrogen/pgTemplate.php";
?>

<!-- Main content: shift it to the right by 250 pixels when the sidebar is visible -->
<div class="w3-main" style="margin-left:250px">

  <div class="w3-row w3-padding-64">
    <div class="w3-twothird w3-container">

	<?php include "Hydrogen/pgLogin.php"; ?>

    </div>
    <div class="w3-third w3-container">
    </div>
  </div>

</div>

<?php include "Hydrogen/elemFooter.php"; ?>
</body></html>

******************************************************/

// Define a login page URL in settingsLogin.php or use the default defined there.
// Your login page may INCLUDE or REQUIRE Hydrogen/pgLogin.php but should not BE this file, as
//    doing that either in PHP code or in a hyperlink will put the user in the Hydrogen 
//    subdirectory rather than the directory for your app and then links will break.
require_once ("Hydrogen/settingsLogin.php");

//Code in this file (pgLogin.php) is independent of authentication method as long as
//  the method can be implemented as a function which takes a username and password
//  as arguments and returns a "1" (one) for success. This function goes in the following file:
require_once('Hydrogen/libAuthenticate.php');

if (!isset($_SESSION['username'])) session_start();


function showUsernameAndLogoutButton() {
	echo ('<table><tr><td>Logged in as </td><td class="username">' . $_SESSION['username'] . "</td></tr></table>");
	echo "<br><br>";
	echo ('	<form class="access" id="logout" action="' . $settings['login_page'] . '" method="post">');
	echo ('	<input type="hidden" name="flow" value="logOut">');
	echo ('	<input type="submit" value="Log out">');
	echo ('	</form>');
}

function showDebugInfo() {
	echo "Debug info:<br>";
	if (isset($_POST['uname'])) 		{ echo ("post uname=" . $_POST['uname'] . "<br>"); }
		else {echo "post uname is empty<br>";}
	if (isset($_POST['flow'])) 			{ echo "flow=" . $_POST['flow'] . "<br>"; }
		else {echo "post flow is empty<br>";}
	if (isset($_SESSION['username'])) 	{ echo "session username (1)=" . $_SESSION['username'] . "<br>"; }
		else {echo "session uname before flow processing is empty<br>";}
	echo "<br>";
};

function logOut() {
	//clear the session variables to log them out
	$_SESSION=array();
}

//showDebugInfo();


//check if this page was called by the click of the "log out" button
if (isset($_POST['flow'])) {
//echo "flow=" . $_POST['flow'];
	if ($_POST['flow']="logOut"){
			logOut();;
	}
}

//We choose to define status of "logged in" as a non-empty $_SESSION['username'} token.
//If the user has already successfully logged in, notify them and offer to log them out
if (isset($_SESSION['username'])) {
	showUsernameAndLogoutButton() ;
	exit();
}


?>

<!-- //-------------all IF blocks have completed by this point-------------------- -->

<?php

//The user is not logged in, so figure out if the user has supplied credentials
//(i.e. whether this page has called itself from the login form submit button)

if (isset($_POST['uname']) and isset($_POST['passwd'])) {

	//the credentials are there, so attempt to authenticate
	//using whatever method is defined in authenticate.php
	if (authenticate($_POST['uname'],$_POST['passwd'])==1) {
		$_SESSION['username']=$_POST['uname'];
		//the user is now logged in
		$_SESSION['password']=$_POST['passwd'];
		unset($_SESSION['errMsg']);
	}


	//Now instead of the authenticate() function we will just
	//use the 'username' token to check login status
	if (isset($_SESSION['username'])) {
		//successful, so show them their status
		showUsernameAndLogoutButton();

		//check if there was a page that the user would want to go
		//back to now that they are done logging in
		if (isset($_SESSION['referring_page'])) {
			echo ('You can return to the page you were viewing before you logged in <a href="' . $_SESSION['referring_page'] . '">here</a>.');
		} // end IF (referred)

		exit();
	} // end IF (authenticated)

} else {$_POST['uname']="";  //define the variable so we can populate the form with it regardless of whether it was blank
} // end IF (post:username)

//eventually, lost password/forgotten username help will be needed;
//put it here . . .

?>

<!-- -----------------------all IF blocks have completed by this point------------------- -->

<!-- display the login form with any error message from a previous authentication attempt -->


<?php
if ($settings['prompt_reg']==1) {
	echo ("<h2>Registered users log in below:</h2>");
}
?>


<?php //showDebugInfo(); ?>

<?php

//We are about to put a POST variable back in the user's browser, so it is
//necessary to sanitize it first to prevent XSS attacks, etc.
$sanitized_uname=filter_var($_POST['uname'],FILTER_SANITIZE_ENCODED);
echo '<form class="access" id="login" action="' . $settings['login_page'] . '" method="post">';
?>

<table><tr><td>
Username </td><td><input type="text" name="uname" id="id" value="
<?php echo $sanitized_uname; ?>
"</td></tr><tr><td>
Password </td><td><input type="password" name="passwd" id="pwd"><br>
<tr><td>
<input type="submit" value="Log in">
</td>

<?php
if (isset($_SESSION['errMsg'])) {
echo ('<td class="error">' . $_SESSION['errMsg'] . "</td>");
}
?>

</tr></table>
</form>

<?php
if ($settings['prompt_reg']==1) {
	echo("<h2>Not a registered user?</h2>");
	echo('<p>Register <a href="' . $settings['registration_page'] . '">here</a>.</p>');
}
?>

