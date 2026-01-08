<?php

// Always set 'referring_page' AFTER including elemLoginStatusbar.php
// It can be made blank below to prevent it having stale information from some other page
// unset($_SESSION['referring_page']);

// Define a login page URL in settingsLogin.php. You may use the default.
// Your login page may INCLUDE or REQUIRE Hydrogen/pgLogin.php but should not BE this file, as
//    doing that either in this code or in a hyperlink will put the user in the Hydrogen 
//    subdirectory rather than the directory for your app.
//require_once ("Hydrogen/settingsLogin.php");
if (!isset($settings['color3']))  $settings['color3']="w3-hover-white";
function showLoginStatus() {
	global $settings;
	if (isset($_SESSION['username'])) {
		showUsername();
	} else {
	echo ('<a href="' . $settings['login_page'] . '" class="w3-bar-item w3-button "' .  
		$settings['color3'] .'>Log in</a>');
	}
}

function showUsername() {
	global $settings;
	echo ('<a href="#" class="w3-bar-item w3-button w3-medium w3-hide-small ' .  
		$settings['color3'] .'">Logged in as ' . $_SESSION['username'] . "</a>");
}

function showLogoutButton() {
	global $settings;
	echo ('	<span class="w3-bar-item w3-button w3-medium"><form id="logout" action="' . $settings['login_page'] .
	 '" method="post">');
	echo ('	<input type="hidden" name="flow" value="logOut">');
	echo ('	<input type="submit" value="Log out">');
	echo ('	</form></span>');
}

?>

<?php 
showLoginStatus(); 
if (isset($_SESSION['username'])) {
	showLogoutButton() ;
}
?>
