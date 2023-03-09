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

//set this to true to enable debug output from this PHP file
$debug[__FILE__]=true;
include_once ('Hydrogen/libDebug.php');
include_once ('settingsHydrogen.php');
include_once ('settingsPasswords.php');

//(all this should come from settingsHydrogen.php):
if (!isset($settings['DEFAULT_DB_TYPE'])) $settings['DEFAULT_DB_TYPE'] = "oracle";
if (!isset($settings['DEFAULT_DB_USER'])) $settings['DEFAULT_DB_USER'] = "scott";
if (!isset($settings['DEFAULT_DB_PASS'])) $settings['DEFAULT_DB_PASS'] = "tiger";
if (!isset($settings['DEFAULT_DB_HOST'])) $settings['DEFAULT_DB_HOST'] = "localhost";
if (!isset($settings['DEFAULT_DB_PORT'])) $settings['DEFAULT_DB_PORT'] = "1521";
if (!isset($settings['DEFAULT_DB_INST'])) $settings['DEFAULT_DB_INST'] = "XE";
if (!isset($settings['DEFAULT_DB_MAXRECS'])) $settings['DEFAULT_DB_MAXRECS'] = 150;

//Here we do some complicated gymnastics to make this code less platform-dependent.
//We define an "OCIDataSource" class using one of the files included below. If the oci extension is not loaded, 
//  we create it as a dummy ('Hydrogen/no-oci.inc'). otherwise we build it to call real OCI functions 
//  ('Hydrogen/oci.inc'). If the default DB type is oracle, 
//  at the end of THIS file we will instantiate an OCIDataSource class and make it the default. If mysql, we will 
//  instantiate the "dateSource" class defined below as the default.
//The function names for both classes are the same (for platform independence), but the GOTCHA 
//  is that when changes are made to THIS file, they may also have to be made in oci.inc; and when troubleshooting, 
//  you should double-check that you are actually looking at the right class definition.
if (extension_loaded('mysqli')) include_once ('Hydrogen/mysqli.inc');
if (extension_loaded('oci8')) include_once ('Hydrogen/oci.inc'); else include_once ('Hydrogen/no-oci.inc');


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
		debug ("Original SQL (limit_sql):" . $sql);
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

		switch ($this->dbType) {
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
		debug("Constructing dataSource class",__FILE__);

		if($dbType=="xNULLx") $dbType=$settings['DEFAULT_DB_TYPE'];
		if($dbUser=="xNULLx") $dbUser=$settings['DEFAULT_DB_USER'];
		if($dbPass=="xNULLx") $dbPass=$settings['DEFAULT_DB_PASS'];
		if($dbHost=="xNULLx") $dbHost=$settings['DEFAULT_DB_HOST'];
		if($dbPort=="xNULLx") $dbPort=$settings['DEFAULT_DB_PORT'];
		if($dbInst=="xNULLx") $dbInst=$settings['DEFAULT_DB_INST'];

		$this->setMaxRecs();
		$this->dbType=$dbType;
		switch ($this->dbType) {
			default:
				//mysql
				debug("Connecting to mysql",__FILE__);
				$this->mysqli=getDBConnection($dbHost, $dbUser, $dbPass,$dbInst);
		}
	}

	public function setMaxRecs($int=0) {
		global $settings;
		if($int==0) $int=$settings['DEFAULT_DB_MAXRECS'];
		$this->maxRecs=$int;
	}

	public function getSQLID() {
		$sqlstring=str_replace("'","#SINGLEQUOT#",$this->unlimited_sql);
		$insert_sql="INSERT INTO saved_sql (session_id,sqltext) values ('". session_id() ."','".$sqlstring ."')";
		$result = $this->mysqli->query($insert_sql) ;
		if (!$result) die ("Error executing SQL:" . $insert_sql . " Message: " . $this->mysqli->error);
		$select_sql="SELECT max(id) FROM saved_sql WHERE session_id='". session_id() ."'";
		$result = $this->mysqli->query($select_sql) ;
		if (!$result) die ("Error querying DB with SQL:" . $select_sql . " Message: " . $this->mysqli->error);
		$result_row = $result->fetch_array(MYSQLI_NUM);
		return $result_row[0];
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
			switch ($this->dbType) {
			default:
				//mysql
				$result = $this->mysqli->query($sql) ;
				if (!$result) die ("Error querying DB with SQL:" . $sql . " Message: " . $this->mysqli->error);
				$this->mysqli_result=$result;

				if (strpos(strtoupper($sql),'INSERT')===0) return $sql;
				else if (strpos(strtoupper($sql),'UPDATE')===0) return $sql;
				else if (strpos(strtoupper($sql),'DELETE')===0) return $sql;
				else {


					//get metadata
					$finfo = $result->fetch_fields();
					$ncols=count($finfo);
					debug ("MySQL result set column count: ".$ncols,__FILE__);
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
		debug ("function: cls_dataSource:pagination",__FILE__);
		$count_sql="SELECT COUNT(*) FROM (" . $this->unlimited_sql . ")";
		switch ($this->dbType) {
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
			default:
				if (!$this->mysqli_result) die ("FATAL ERROR: Invalid cursor. This may be due to having updated the underlying dataset between fetching rows.");
				if ($arraytype=="indexed") {
					$result_row = $this->mysqli_result->fetch_array(MYSQLI_NUM);
				} else {
					$result_row = $this->mysqli_result->fetch_array(MYSQLI_ASSOC);
				}
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


}

if (!isset($dataSource['default'])) {
	debug("Creating default data source",__FILE__);
	global $settings;
	if ($settings['DEFAULT_DB_TYPE']=="oracle") $dataSource['default']=new OCIDataSource();
	if ($settings['DEFAULT_DB_TYPE']=="mysql") $dataSource['default']=new dataSource();
}
	$dds = $dataSource['default'];
?>
