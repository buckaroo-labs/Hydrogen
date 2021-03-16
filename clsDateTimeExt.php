<?php

/* 
Some functions are defined in a class simply to help with 
code readability. When I see "ClassName::FunctionName()" then I know
to look in the file "clsClassName.php" for the function definition
*/

class DateTimeExt extends DateTime{
	
	//"20181231T235900Z"
	public static  $CalDAVZFormat="Ymd\THis\Z";
	
	//"2018-12-31 23:59:00"
	public static  $MySQLFormat='Y-m-d H:i:s';
	
	//"2018-12-31 23:59:00 -00:00"
	public static  $OffsetFormat='Y-m-d H:i:s P';
	
	/*
	Common time formats for constructor:
	(https://www.php.net/manual/en/datetime.formats.compound.php)
	MySQL: "2018-12-31 23:59:00"
	*/
	
	public static function zdate($intTimestamp=0) {
		if ($intTimestamp==0) return gmdate(DateTimeExt::$CalDAVZFormat); else return gmdate(DateTimeExt::$CalDAVZFormat,$intTimestamp);
	}
	
	//deprecate
	public static function DateTimeFromCalDAVZFormat($CalDAVGMTDateTimeString) {
		$dateobj = DateTimeExt::createFromFormat(DateTimeExt::$CalDAVZFormat, $CalDAVGMTDateTimeString, new DateTimeZone("UTC"));
		$dateobj->setTimezone(new DateTimeZone(date_default_timezone_get()));
		return $dateobj;
	}
	
	//deprecate
	public function MySQLDate($dateobj) {
		return $dateobj->format(DateTimeExt::$MySQLFormat);
	}
	
	//deprecate
	public static function CalDAVZFormatFromMySQLDateTime($MySQLDateTime) {
		$dateobj = new DateTime($MySQLDateTime);
		$dateobj->setTimezone(new DateTimeZone("UTC"));
		return $dateobj->format(DateTimeExt::$CalDAVZFormat);
	}
	
	//All of the following functions are too simple to be of much use
	// other than to map out in one place how all the conversions work
	// between DateTime object, format string, and timestamp int.
	public static function objFromString($strDateTime) {
		$dateobj = new DateTimeExt($strDateTime);
		return $dateobj;
	}
	
	public static function objFromInt($time) {
		$dateobj = new DateTimeExt();
		$dateobj->setTimestamp($time);
		return $dateobj;
	}
	
	public static function objNow() {
		$dateobj = new DateTimeExt();
		return $dateobj;
	}
	
	public function toInt() {
		return $this->getTimestamp();
	}
	
	public function toString($format) {
		return $this->format($format);
	}
	
	public function setTZ ($TZ="") {
		if ($TZ=="") $TZ=date_default_timezone_get();
		$this->setTimezone(new DateTimeZone($TZ));
	}
	
}

?>