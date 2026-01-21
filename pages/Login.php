
<?php
/*****************************************************

EXAMPLE USAGE:
<?php
$pagetitle="Log In | MySite";
include "Hydrogen/pgTemplate.php";
?>

<!-- Main content: shift it to the right when the sidebar is visible -->
<div class="w3-main">

  <div class="w3-row w3-padding-64">
    <div class="w3-twothird w3-container">

	<?php include "Hydrogen/pages/Login.php"; ?>

    </div>
    <div class="w3-third w3-container">
    </div>
  </div>

</div>

<?php include "Hydrogen/elements/Footer.php"; 
	include "Hydrogen/elements/Navbar.php"
?>
</body></html>

******************************************************/

// Define a login page URL in settingsLogin.php or use the default defined there.
// Your login page may INCLUDE or REQUIRE Hydrogen/pages/Login.php but should not BE this file, as
//    doing that either in PHP code or in a hyperlink will put the user in the Hydrogen
//    subdirectory rather than the directory for your app and then links will break.
require_once ("Hydrogen/settingsLogin.php");
if (!isset($settings['login_page'])) $settings['login_page'] = "index.php?p=login";
//$done_authenticating=false;
//Code in this file (pgLogin.php) is independent of authentication method as long as
//  the method can be implemented as a function which takes a username and password
//  as arguments and returns a "1" (one) for success. This function goes in the following file:
require_once('Hydrogen/lib/Authenticate.php');
require_once('Hydrogen/lib/State.php');

if (session_status() == PHP_SESSION_NONE) session_start();


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



//showDebugInfo();

//2025-12-08 moving this code to pgTemplate.php to be able to process cookies

//check if this page was called by the click of the "log out" button
/*
if (isset($_POST['flow'])) {
//echo "flow=" . $_POST['flow'];
	if ($_POST['flow']="logOut"){
			logOut();;
	}
}

function logOut() {
	global $settings;
	//clear the session variables to log them out
	$_SESSION=array();

	//2025-12-08
	//remove the persistent login cookie data and expire it
	//setcookie($settings['JWTTokenName'], "", time() - 3600);

}
*/

//We define status of "logged in" as a non-empty $_SESSION['username'} token.
//If the user has already successfully logged in, notify them and offer to log them out

	//The user is not logged in, so figure out if the user has supplied credentials
	//(i.e. whether this page has called itself from the login form submit button)
	//2025-12-08 move much of this to pgTemplate to handle cookies


	//eventually, lost password/forgotten username help will be needed;
	//put it here . . .


	// -----------------------all IF blocks have completed by this point------------------- -->

	// display the login form with any error message from a previous authentication attempt -->



	if (!isset($done_authenticating) || !$done_authenticating) {
		if ($settings['prompt_reg']==1) {
			echo ("<h2>Registered users log in below:</h2>");
		}

		//We are about to put a POST variable back in the user's browser, so it is
		//necessary to sanitize it first to prevent XSS attacks, etc.
		$sanitized_uname=filter_var($_POST['uname'],FILTER_SANITIZE_EMAIL);
		echo '<form class="access" id="login" action="' . $settings['login_page'] . '" method="post">';

		echo '<table><tr><td>'. $settings['uname_label']. '</td><td><input type="text" name="uname" id="id" value="';
		echo $sanitized_uname; 
		echo '"></td></tr><tr><td>Password </td><td><input type="password" name="passwd" id="pwd"><br><tr><td><input name="btnSubmit" type="submit" value="Log in"></td>';


		if (isset($_SESSION['errMsg'])) {
			echo ('<td class="error">' . $_SESSION['errMsg'] . "</td>");
		}


		echo"</tr></table></form>";

		if ($settings['prompt_reg']==1 && array_key_exists('SMTPHost',$settings)) {
			echo("<h2>New user or forgotten password?</h2>");
			echo('<p>Reset or request password <a href="' . $settings['registration_page'] . '">here</a>.</p>');
		}
	} else {
		//redirect?
		echo '<p style="margin-bottom: 400px;">Logged in.</p>';
	}

?>

