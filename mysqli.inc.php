<?php
function getDBConnection($dbHost, $dbUser, $dbPass,$dbInst) {
		$CONN= new mysqli($dbHost, $dbUser, $dbPass,$dbInst);
		if (mysqli_connect_errno()) {
		    die ("MySQL connection to database '$dbInst' on '$dbHost' failed for username '$dbUser': ".  mysqli_connect_error());
		}
		return $CONN;
}


?>