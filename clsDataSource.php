<?php
/* Including this class file will:
set default db connection string values as defined in settings.php
create an array for dataSource objects
create an array for saved SQL objects
define the dataSource class with several handy functions
instantiate dataSource class with defaults and save as 'default' member of datasource array
provide the short-name $dds reference to 'default' data source


USAGE:
STEP ONE
(Assuming that this file has already been included)
--The following example code(using the default data source) will specify that records should be returned in 'pages' of 50,
$result = $dds->setMaxRecs(50);
--then send the query,
$result = $dds->setSQL($sql);
--and then get back a page count so that the UI can display it.
$page_count = $dds->getPageCount();

STEP TWO
--The following code will use the dataSource class as well as the HTMLTable class (defined elsewhere) 
--to format the records fetched from the database into an HTML table
--(See clsHTMLTable.php for particulars on the HTMLTable class)
$table=new HTMLTable($dds->getFieldNames(),$dds->getFieldTypes());
$table->start();
while ($result_row = $dds->getNextRow()){
	$table->addRow($result_row);
}
$table->finish();

*/

include_once ('Hydrogen/libDebug.php');
include_once ('settingsHydrogen.php');

//(all this should come from settingsHydrogen.php):
if (!isset($settings['DEFAULT_DB_TYPE'])) $settings['DEFAULT_DB_TYPE'] = "oracle";
if (!isset($settings['DEFAULT_DB_USER'])) $settings['DEFAULT_DB_USER'] = "scott";
if (!isset($settings['DEFAULT_DB_PASS'])) $settings['DEFAULT_DB_PASS'] = "tiger";
if (!isset($settings['DEFAULT_DB_HOST'])) $settings['DEFAULT_DB_HOST'] = "localhost";
if (!isset($settings['DEFAULT_DB_PORT'])) $settings['DEFAULT_DB_PORT'] = "1521";
if (!isset($settings['DEFAULT_DB_INST'])) $settings['DEFAULT_DB_INST'] = "XE";
if (!isset($settings['DEFAULT_DB_MAXRECS'])) $settings['DEFAULT_DB_MAXRECS'] = 150;


$dataSource=array();
$savedSQL=array();

class dataSource {
	//This class provides common functionality for various database brands. 
	//1. The constructor provides connectivity
	//2. Function "setSQL" parses a SQL statement and retrieves metadata
	//Known and tested database types: Oracle, MySQL.

	protected $dbconn;
	protected $maxRecs;
	protected $cursor;
	protected $stmt;
	protected $mysqli;
	protected $mysqli_result;
	protected $dbType;
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
		if (strpos('.'.strtoupper($sql),'ALTER')==1) return $sql;
		if (strpos('.'.strtoupper($sql),'UPDATE')==1) return $sql;
		
		if (!isset($this->page_num)) $this->page_num=1;
		debug("Page num: $this->page_num");
		if (isset($this->page_count)) {
			if ($this->page_num > $this->page_count) $this->page_num=$this->page_count;
		}
		$start_rec=(($this->page_num-1)*$this->maxRecs)+1;
		$end_rec=$start_rec+$this->maxRecs-1;

		switch ($this->dbType) {
			case 'oracle':
				//ugh  . . .
				//http://www.oracle.com/technetwork/issue-archive/2006/06-sep/o56asktom-086197.html
				$prepend="select * from ( select /*+ FIRST_ROWS(n) */   a.*, ROWNUM rnum from ( ";
				$append=") a where ROWNUM <=  " . ($start_rec + $this->maxRecs - 1) . ")  where rnum  >= $start_rec";
				return $prepend . " " . $sql . " " . $append;
				break;
			default:
				return $sql . " " . " limit " . ($start_rec - 1) . " , " . $this->maxRecs;
		}
	}

	public function __construct(
		$dbType="xNULLx",
		$dbUser="xNULLx",
		$dbPass="xNULLx",
		$dbHost="xNULLx",
		$dbPort="xNULLx",
		$dbInst="xNULLx") {
		global $settings;
		debug("Constructing dataSource class");
		
		if($dbType=="xNULLx") $dbType=$settings['DEFAULT_DB_TYPE'];
		if($dbUser=="xNULLx") $dbUser=$settings['DEFAULT_DB_USER'];
		if($dbPass=="xNULLx") $dbPass=$settings['DEFAULT_DB_PASS'];
		if($dbHost=="xNULLx") $dbHost=$settings['DEFAULT_DB_HOST'];
		if($dbPort=="xNULLx") $dbPort=$settings['DEFAULT_DB_PORT'];
		if($dbInst=="xNULLx") $dbInst=$settings['DEFAULT_DB_INST'];
		
		$this->setMaxRecs();
		$this->dbType=$dbType;
		switch ($this->dbType) {
			case 'oracle':
				debug("Connecting to Oracle");
			    $dbstring=$dbHost . ":" . $dbPort . "/" . $dbInst;
				$this->dbconn = oci_connect($dbUser, $dbPass, $dbstring) or die("Connection to DB failed." . oci_error());
				$this->setSQL("alter session SET NLS_DATE_FORMAT = 'RRRR-MM-DD HH24:MI:SS'" );
				break;
			default:
				//mysql
				debug("Connecting to mysql");
				$this->mysqli=new mysqli($dbHost, $dbUser, $dbPass,$dbInst);
				if (mysqli_connect_errno()) {
				    die ("Connect failed: ".  mysqli_connect_error());
				}
				//$this->dbconn = mysql_connect($dbHost, $dbUser, $dbPass) or die("Connection to DB failed." . mysql_error());
				//$result = mysql_select_db($dbInst, $this->dbconn) or die("Error selecting DB." . mysql_error());
		}
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
		debug("class-limited SQL: $sql");
			switch ($this->dbType) {

			case 'oracle':
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
				break;
			default:
				//mysql
				$result = $this->mysqli->query($sql) or die ("Error querying DB with SQL:" . $sql . " Message: " . $this->mysqli->error);
				$this->mysqli_result=$result;

				if (strpos(strtoupper($sql),'INSERT')===0) return $sql;
				else if (strpos(strtoupper($sql),'UPDATE')===0) return $sql;
				else {


					//get metadata
					$finfo = $result->fetch_fields();
					$ncols=count($finfo);
					debug ("MySQL result set column count: ".$ncols);
					$i=1;
					foreach ($finfo as $val) {
							$this->colNames[$i-1] = $val->name;
							$this->colTypes[$i-1] = $val->type;
							$i++;
					}
				}
			}
	}

	function paginate() {
		debug ("function: cls_dataSource:pagination");
		$count_sql="SELECT COUNT(*) FROM (" . $this->unlimited_sql . ")";
		switch ($this->dbType) {
			case 'oracle':
				//Parse the statement
				$stmt = oci_parse($this->dbconn,$count_sql) or die ( oci_error($this->dbconn));
				//execute the query
				$result= oci_execute($stmt) or die ("Error querying DB with SQL:" . $count_sql . " Error message: " . oci_error($this->dbconn));
				$result_row = oci_fetch_array($stmt,OCI_NUM+OCI_RETURN_NULLS);
				break;
			default:
				//mysql
				$count_sql=$count_sql . " as aggr";
				$result = $this->mysqli->query($count_sql) or die ("Error querying DB with SQL:" . $count_sql . " Message: " . $this->mysqli->error);
				$result_row = $result->fetch_array(MYSQLI_NUM);
		}
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
		switch ($this->dbType) {
			case "oracle":
				if ($arraytype=="indexed") {
					$result_row = oci_fetch_array($this->stmt,OCI_NUM+OCI_RETURN_NULLS);
				} else {
					$result_row = oci_fetch_array($this->stmt,OCI_ASSOC+OCI_RETURN_NULLS);
				}
				break;
			default:
				if ($arraytype=="indexed") {
					$result_row = $this->mysqli_result->fetch_array(MYSQLI_NUM);
				} else {
					$result_row = $this->mysqli_result->fetch_array(MYSQLI_ASSOC);
				}
			}
		return $result_row;
	}

	function getDataset() {
			$rownum=0;
			while ($result_rows[$rownum] = $this->getNextRow()){
					$rownum++;
			}
			return $result_rows;
	}


}

if (!isset($dataSource['default'])) {
	debug("Creating default data source");
	$dataSource['default']=new dataSource();
}
	$dds = $dataSource['default'];
?>
