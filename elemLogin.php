<?php

//Always set 'referring_page' AFTER including this file
//It is made blank below to prevent it having stale information from some other page
//unset($_SESSION['referring_page']);

function showLoginStatus() {
	if (isset($_SESSION['username'])) {
		showUsername();
	} else {
	echo ('<a href="/login.php" class="statusbar_item">Log in</a>');
	}
}

function showUsername() {
	echo ('<div class="loginStatus">Logged in as ' . $_SESSION['username'] . "</div> ");
}

function showLogoutButton() {
	echo ('	<li class="statusbar"><form class="access" id="logout" action="/login.php" method="post">');
	echo ('	<input type="hidden" name="flow" value="logOut">');
	echo ('	<input type="submit" value="Log out">');
	echo ('	</form></li>');
}

?>


<ul class="statusbar" id="login_elem">
<li class="statusbar">
<?php showLoginStatus() ?>
</li>

<?php if (isset($_SESSION['username'])) {
	showLogoutButton() ;
}
 ?>

</ul>
