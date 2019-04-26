<?php

//Always set 'referring_page' AFTER including elemLoginStatusbar.php
//It can be made blank below to prevent it having stale information from some other page
//unset($_SESSION['referring_page']);

function showLoginStatus() {
	if (isset($_SESSION['username'])) {
		showUsername();
	} else {
	echo ('<a href="/Hydrogen/login.php" class="statusbar_item">Log in</a>');
	}
}

function showUsername() {
	echo ('<div class="loginStatus">Logged in as ' . $_SESSION['username'] . "</div> ");
}

function showLogoutButton() {
	echo ('	<li class="statusbar"><form class="access" id="logout" action="/Hydrogen/login.php" method="post">');
	echo ('	<input type="hidden" name="flow" value="logOut">');
	echo ('	<input type="submit" value="Log out">');
	echo ('	</form></li>');
}

?>

<table class="statusbar">
<tbody>
<tr>
<td>
<ul class="statusbar">
<li class="statusbar">
<?php showLoginStatus() ?>
</li>

<?php if (isset($_SESSION['username'])) {
	showLogoutButton() ;
}
 ?>

</ul>
</td>
</tr>
</tbody>
</table>