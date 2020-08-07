<?php
function getOCIConnection($dbUser, $dbPass, $dbstring) {
	return oci_connect($dbUser, $dbPass, $dbstring) or die("Connection to DB failed." . oci_error());
}

function getOCIStatement($conn,$sql) {
	$retOBJ=oci_parse($conn,$sql) or die ( oci_error($this->dbconn));
	return $retOBJ;
}

function getOCIResult($stmt) {
	$result= oci_execute($stmt) or die ("Error querying DB with SQL:" . $sql . " Error message: " . oci_error($this->dbconn));
	return $result;
}

function getOCIFieldCount($stmt) {
	return oci_num_fields($stmt)
}

function getOCIFieldName($stmt,$index) {
	return oci_field_name($stmt, $index);
}

function getOCIFieldType($stmt,$index) {
	return oci_field_type($stmt, $index);
}

function fetchOCINonIndexedRow($stmt) {
	return oci_fetch_array($stmt,OCI_ASSOC+OCI_RETURN_NULLS);

}

function fetchOCIIndexedRow($stmt) {
	return oci_fetch_array($stmt,OCI_NUM+OCI_RETURN_NULLS)

}

?>