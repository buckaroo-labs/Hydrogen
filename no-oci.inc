<?php
//dummy functions provided to match functions in oci.inc when the OCI extension is not loaded


class OCIDataSource {
		public function __construct($dbUser="xNULLx",
		$dbPass="xNULLx",
		$dbHost="xNULLx",
		$dbPort="xNULLx",
		$dbInst="xNULLx") {
			error_log ("Oracle OCI extension not detected! Check php.ini (or php -m) for loaded extensions;check expected extension name in htdocs/Hydrogen/clsDatasource.php; or change DEFAULT_DB_TYPE in settingsHydrogen.php. ");
			
		}


}
?>