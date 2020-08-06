<?php
//dummy functions provided to match functions in oci.inc when the OCI extension is not loaded

function getOCIConnection($dbHost, $dbUser, $dbPass) {
	return 0;
}

function getOCIStatement($conn,$sql) {
	return 0;
}

function getOCIResult($stmt) {
	return 0;
}

function getOCIFieldCount($input) {
	return 0;
}
function getOCIFieldName($stmt,$index) {
	return 0;
}
function getOCIFieldType($stmt,$index) {
	return 0;
}

function fetchOCINonIndexedRow($stmt) {
	return 0;
}

function fetchOCIIndexedRow($stmt) {
	return 0;
}

?>