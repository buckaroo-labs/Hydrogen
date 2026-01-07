<?php
function getDBConnection3($private) {
	global $settings;
	//$private argument, if true, returns a database available 
	//only to the logged-in user. ignored if user is unauthenticated.
	$username='default';
	$secret_key=$settings['JWT-SECRET-KEY'];
	if (!empty($settings['SQLITE-SECRET-KEY'])) $secret_key=$settings['SQLITE-SECRET-KEY'];
	if (isset($_SESSION['username']) && $private) {
		$username=strtolower($_SESSION['username']);
	}
	//a filename containing a hash of the username and key can be exposed to the
	// user for download without compromising the user's data or the key
	// even if this code is open source
	$filename = $username . "_" . md5($username.$secret_key);
	$dbString='sqlite:' . $filename . '.sqlite';
	$dbconn = new SQLite3($dbString);
	return $dbconn;
}

/*
// Create tables if not existing
$db->exec("
    CREATE TABLE IF NOT EXISTS projects (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        status TEXT 
    );

    CREATE TABLE IF NOT EXISTS time_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        project_id INTEGER,
        start_time TEXT,
        end_time TEXT,
        notes TEXT,
        FOREIGN KEY(project_id) REFERENCES projects(id)
    );
");
?>

*/

class SQLiteDataSource {

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
		return $sql . " LIMIT  " .  $this->maxRecs . " OFFSET " . $start_rec;

	}

	public function __construct($private=false) {
		global $settings;
		debug("Constructing SQLiteDataSource class",__FILE__);
		$this->setMaxRecs();
		$this->dbconn = getDBConnection3($private);
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
		$this->unlimitedSQL=$unlimited_sql;
		$sql=$this->limitSQL($unlimited_sql);
		$this->colNames=array();
		$this->colTypes=array();
		debug("class-limited SQL: $sql",__FILE__);
		$this->dbconn->exec($sql);
		$this->stmt=$this->dbconn->query($sql);
		$ncols = $this->stmt->numColumns();
		for ($i = 1; $i <= $ncols; $i++) {
			$this->colNames[$i-1] = $this->stmt->columnName($i);
			$this->colTypes[$i-1] = $this->stmt->columnType($i);
		}

	}
	function paginate() {
		debug ("function: cls_dataSource:pagination",__FILE__);
		$count_sql="SELECT COUNT(*) as recct FROM (" . $this->unlimitedSQL . ")";
		//Parse the statement
		$res=$this->dbconn->query($count_sql);
		$row = $res->fetchArray();
		$rec_count=$row['recct'];
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
		return $this->stmt->fetchArray();
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