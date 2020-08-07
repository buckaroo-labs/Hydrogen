<?php
function getDBConnection($dbHost, $dbUser, $dbPass,$dbInst) {
		$CONN= new mysqli($dbHost, $dbUser, $dbPass,$dbInst);
		if (mysqli_connect_errno()) {
		    die ("MySQL connection failed: ".  mysqli_connect_error());
		}
		return $CONN;
}


?>