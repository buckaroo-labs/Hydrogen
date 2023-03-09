<?php

class OCIDataSource {

	//1. The constructor provides connectivity
	//2. Function "setSQL" parses a SQL statement and retrieves metadata


	protected $dbconn;
	protected $maxRecs;
	protected $cursor;
	protected $stmt;
	protected $colNames;
	protected $colTypes;
	protected $unlimitedSQL;
	protected $page_count;
	protected $page_num;

	function limitSQL($sql) {
		//This function will take a SQL statement and append a clause limiting the
		//number of rows returned, starting at the appropriate offset.

		//INSERT, UPDATE, and DELETE statements will be unaffected
		if (strpos('.'.strtoupper($sql),'INSERT')==1) return $sql;
		if (strpos('.'.strtoupper($sql),'DELETE')==1) return $sql;
		if (strpos('.'.strtoupper($sql),'UPDATE')==1) return $sql;
		if (strpos('.'.strtoupper($sql),'ALTER')==1) return $sql;
		if (strpos('.'.strtoupper($sql),'BEGIN')==1) return $sql;
		if (!isset($this->page_num)) $this->page_num=1;
		debug("Page num: $this->page_num",__FILE__);
		if (isset($this->page_count)) {
			if ($this->page_num > $this->page_count) $this->page_num=$this->page_count;
		}
		$start_rec=(($this->page_num-1)*$this->maxRecs)+1;
		$end_rec=$start_rec+$this->maxRecs-1;

				//ugh  . . .
				//http://www.oracle.com/technetwork/issue-archive/2006/06-sep/o56asktom-086197.html
				$prepend="select * from ( select /*+ FIRST_ROWS(n) */   a.*, ROWNUM as row# from ( ";
				$append=") a where ROWNUM <=  " . ($start_rec + $this->maxRecs - 1) . ")  where row#  >= $start_rec";
				return $prepend . " " . $sql . " " . $append;

	}

	public function __construct(
		$dbUser="xNULLx",
		$dbPass="xNULLx",
		$dbHost="xNULLx",
		$dbPort="xNULLx",
		$dbInst="xNULLx") {
		global $settings;
		debug("Constructing OCIDataSource class",__FILE__);

		if($dbUser=="xNULLx") $dbUser=$settings['DEFAULT_DB_USER'];
		if($dbPass=="xNULLx") $dbPass=$settings['DEFAULT_DB_PASS'];
		if($dbHost=="xNULLx") $dbHost=$settings['DEFAULT_DB_HOST'];
		if($dbPort=="xNULLx") $dbPort=$settings['DEFAULT_DB_PORT'];
		if($dbInst=="xNULLx") $dbInst=$settings['DEFAULT_DB_INST'];

		$this->setMaxRecs();
		//$this->dbType=$dbType;
				$dbstring=$dbHost . ":" . $dbPort . "/" . $dbInst;
				debug("Connecting to Oracle: $dbstring",__FILE__);
				$this->dbconn = oci_connect($dbUser, $dbPass, $dbstring) or die("Connection to $dbUser at $dbString failed: " . implode(" | ",oci_error()));
				debug("Connected to Oracle",__FILE__);
				$this->setSQL("alter session SET NLS_DATE_FORMAT = 'RRRR-MM-DD HH24:MI:SS'" );

	}

	public function setMaxRecs($int=0) {
		global $settings;
		if($int==0) $int=$settings['DEFAULT_DB_MAXRECS'];
		$this->maxRecs=$int;
	}

	public function setPageNum($page_num) {
		$this->page_num=$page_num;
		if ($this->page_num < 1) $this->page_num=1;
		if (isset($this->page_count)) {
			if ($page_num > $this->page_count) $page_num=$this->page_count;
		}
	}

	function setSQL($unlimited_sql) {
		unset($this->page_count);
		$this->unlimited_sql=$unlimited_sql;
		$sql=$this->limitSQL($unlimited_sql);
		$this->colNames=array();
		$this->colTypes=array();
		debug("class-limited SQL: $sql",__FILE__);
				//Parse the statement
				$stmt = oci_parse($this->dbconn,$sql) or die ( oci_error($this->dbconn));
				//execute the query
				$result= oci_execute($stmt) or die ("Error querying DB with SQL:" . $sql . " Error message: " . oci_error($this->dbconn));
				$this->stmt=$stmt;
				//get metadata
				$ncols=oci_num_fields($this->stmt);
				for ($i = 1; $i <= $ncols; $i++) {
					$this->colNames[$i-1] = oci_field_name($stmt, $i);
					$this->colTypes[$i-1] = oci_field_type($stmt, $i);
				}

	}
	public function getSQLID() {
		$sqlstring=str_replace("'","#SINGLEQUOT#",$this->unlimited_sql);
		$insert_sql="INSERT INTO saved_sql (session_id,sqltext) values ('". session_id() ."','".$sqlstring ."')";
		//Parse the statement
		$stmt = oci_parse($this->dbconn,$insert_sql) or die ( oci_error($this->dbconn));
		//execute the query
		$result= oci_execute($stmt) or die ("Error executing SQL:" . $insert_sql . " Error message: " . oci_error($this->dbconn));

		$select_sql="SELECT max(id) FROM saved_sql WHERE session_id='". session_id() ."'";
		//Parse the statement
		$stmt = oci_parse($this->dbconn,$select_sql) or die ( oci_error($this->dbconn));
		//execute the query
		$result= oci_execute($stmt) or die ("Error executing SQL:" . $select_sql . " Error message: " . oci_error($this->dbconn));

		$result_row = oci_fetch_array($stmt,OCI_NUM+OCI_RETURN_NULLS);
		return $result_row[0];
	}

	function paginate() {
		debug ("function: cls_dataSource:pagination",__FILE__);
		$count_sql="SELECT COUNT(*) FROM (" . $this->unlimited_sql . ")";
				//Parse the statement
				$stmt = oci_parse($this->dbconn,$count_sql) or die ( oci_error($this->dbconn));
				//execute the query
				$result= oci_execute($stmt) or die ("Error querying DB with SQL:" . $sql . " Error message: " . oci_error($this->dbconn));
				$result_row = oci_fetch_array($stmt,OCI_NUM+OCI_RETURN_NULLS);

		$rec_count=$result_row[0];
		if ($this->maxRecs==0) $this->maxRecs==$rec_count;
		$this->page_count=ceil($rec_count/$this->maxRecs);
	}

	function getPageCount() {
		if (!isset($this->page_count)) $this->paginate();
		return $this->page_count;
	}

	public function getFieldNames() {
			return $this->colNames;
	}

	public function getFieldTypes() {
			return $this->colTypes;
	}

	public function getInt($sql) {
		//Assuming the SQL has an integer as the expected return type,
		//Grab the next row and return that integer
		$result = $this->setSQL($sql);
		//if ($result_row=$this->getNextRow()) {
				$result_row=$this->getNextRow();
				$int=(int)$result_row[0]; //or die ("getInt: Data type conversion error with SQL: " .$sql . " and result data:" . $result_row[0] );
				return $int;
		//}
	}

	public function getString($sql) {
		//Assuming the SQL has  a string as the expected return type,
		//Grab the next row and return that string
		$result = $this->setSQL($sql);
		//if ($result_row=$this->getNextRow()) {
				$result_row=$this->getNextRow();
				$str=(string)$result_row[0]; //or die ("getString: Data type conversion error with SQL: " . $sql);
				return $str;
		//}
	}

	public function getNextRow($arraytype="indexed") {
				if ($arraytype=="indexed") {
					$result_row = oci_fetch_array($this->stmt,OCI_NUM+OCI_RETURN_NULLS);
				} else {
					$result_row = oci_fetch_array($this->stmt,OCI_ASSOC+OCI_RETURN_NULLS);
				}
		return $result_row;
	}

	function getDataset($arraytype="indexed") {
			$rownum=0;
			$return=array();
			while ($result_rows[$rownum] = $this->getNextRow($arraytype)){
					$return[$rownum]=$result_rows[$rownum];
					$rownum++;
			}
			return $return;
	}

}//end class
?>