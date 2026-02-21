<?php
function getDBConnection3($private) {
	global $settings;
	//$private argument, if true, returns a database available 
	//only to the logged-in user. ignored if user is unauthenticated.
	$username='default';
	$secret_key=$settings['JWT_SECRET_KEY'];
	if (!empty($settings['SQLITE-SECRET-KEY'])) $secret_key=$settings['SQLITE-SECRET-KEY'];
	if (isset($_SESSION['username']) && $private) {
		$username=strtolower($_SESSION['username']);
	}
	//a filename containing a hash of the username and key can be exposed to the
	// user for download without compromising the key or the user's data 
	// even if this code is open source. But people make mistakes, so if you give a
	// user a copy of their file, rename the copy.
	$filename = $username . "_" . md5($username.$secret_key);
	$dbString='Hydrogen/data/' . $filename . '.sqlite';
	$dbconn = new SQLite3($dbString);
	return $dbconn;
}


// Create tables if not existing
$db=getDBConnection3(false);
$db->exec("

	CREATE TABLE IF NOT EXISTS page_usage (
	id INTEGER  PRIMARY KEY  AUTOINCREMENT,
	server TEXT DEFAULT NULL,
	ip TEXT DEFAULT NULL,
	remote_host TEXT DEFAULT NULL,
	uri TEXT DEFAULT NULL,
	username TEXT DEFAULT NULL,
	server_time datetime DEFAULT CURRENT_TIMESTAMP,
	request_method TEXT DEFAULT NULL
	);


	CREATE TABLE IF NOT EXISTS privilege (
	id INTEGER  PRIMARY KEY  AUTOINCREMENT,
	name TEXT NOT NULL,
	description TEXT NOT NULL,
	ins_user TEXT NOT NULL DEFAULT 'system',
	ins_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
	);

	CREATE TABLE IF NOT EXISTS role (
	id INTEGER  PRIMARY KEY  AUTOINCREMENT,
	name TEXT NOT NULL,
	description TEXT NOT NULL,
	ins_user TEXT NOT NULL DEFAULT 'system',
	ins_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
	);


	CREATE TABLE IF NOT EXISTS user (
	id INTEGER PRIMARY KEY  AUTOINCREMENT , 
	username TEXT NOT NULL,
	email TEXT NOT NULL,
	password_hash TEXT DEFAULT NULL,
	first_name TEXT DEFAULT NULL,
	last_name TEXT DEFAULT NULL ,
	reset_code TEXT DEFAULT NULL,
	session_id TEXT DEFAULT NULL,
	access_token TEXT DEFAULT NULL,
	last_ip TEXT DEFAULT NULL,
	last_login datetime DEFAULT NULL,
	ins_user TEXT NOT NULL DEFAULT 'system',
	ins_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UNIQUE   (username),
	UNIQUE   (email)
	);

	
	CREATE TABLE IF NOT EXISTS saved_sql (
	id INTEGER PRIMARY KEY AUTOINCREMENT , 
	session_id TEXT NOT NULL  ,
	sqltext TEXT NOT NULL  ,
	created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP 
	);

	CREATE TABLE IF NOT EXISTS m_role_privilege (
	id INTEGER  PRIMARY KEY AUTOINCREMENT,
	role_id INTEGER  NOT NULL,
	privilege_id INTEGER  NOT NULL,
	ins_user TEXT NOT NULL DEFAULT 'system',
	ins_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (privilege_id) REFERENCES privilege (id),
	FOREIGN KEY (role_id) REFERENCES role (id),
	CONSTRAINT role_priv_unq UNIQUE (privilege_id,role_id)
	);


	CREATE TABLE IF NOT EXISTS m_user_role (
	id INTEGER  PRIMARY KEY AUTOINCREMENT,
	role_id INTEGER  NOT NULL,
	user_id INTEGER  NOT NULL,
	ins_user TEXT NOT NULL DEFAULT 'system',
	ins_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	CONSTRAINT user_role_unq UNIQUE (user_id,role_id),
	FOREIGN KEY (role_id) REFERENCES role (id),
	FOREIGN KEY (user_id) REFERENCES user (id)
	);

");


class SQLiteDataSource {

	//1. The constructor provides connectivity
	//2. Function "setSQL" parses a SQL statement and retrieves metadata


	protected $dbconn;
	protected $dbType;
	protected $maxRecs;
	protected $cursor;
	protected $stmt;
	protected $colNames;
	protected $colTypes;
	protected $unlimitedSQL;
	protected $page_count;
	protected $page_num;
	protected $error_msg;

	public function dbType() {
		return $this->dbType;
	}

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
		$start_rec=(($this->page_num-1)*$this->maxRecs);
		$end_rec=$start_rec+$this->maxRecs-1;
		return $sql . " LIMIT  " .  $this->maxRecs . " OFFSET " . $start_rec;

	}

	public function __construct($private=false) {
		global $settings;
		debug("Constructing SQLiteDataSource class",__FILE__);
		$this->setMaxRecs();
		$this->dbType='sqlite3';
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
		$row = $this->dbconn->query("PRAGMA database_list")->fetchArray(SQLITE3_ASSOC);
		debug("Querying SQLite db at " . $row['file']);
		unset($this->page_count);
		$this->unlimitedSQL=$unlimited_sql;
		$sql=$this->limitSQL($unlimited_sql);
		$this->colNames=array();
		$this->colTypes=array();
		debug("class-limited SQL: $sql",__FILE__);
		//$this->dbconn->exec($sql);
		$this->stmt=$this->dbconn->query($sql);
		if(!$this->stmt) {
			debug($this->dbconn->lastErrorMsg(),"sqlite3.inc.php:185");
			$this->error_msg=$this->dbconn->lastErrorMsg();
			return false;
		} else {
			$ncols = $this->stmt->numColumns();
			for ($i = 1; $i <= $ncols; $i++) {
				$this->colNames[$i-1] = $this->stmt->columnName($i);
				$this->colTypes[$i-1] = $this->stmt->columnType($i);
			}
			return true;
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

	public function getDataset($arraytype="indexed") {
			$rownum=0;
			$return=array();
			while ($result_rows[$rownum] = $this->getNextRow($arraytype)){
					$return[$rownum]=$result_rows[$rownum];
					$rownum++;
			}
			return $return;
	}

	public function prepare($sql) {
		return $this->dbconn->prepare($sql);
	}

	public function getStmtResult($stmt) {
		return $stmt->execute();
	}

	public function getError() {
		return $this->dbconn->lastErrorMsg();
	}
}//end class
?>