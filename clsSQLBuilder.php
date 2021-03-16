<?php

//This class facilitates the building of simple SQL statements at runtime.

class SQLBuilder{

	//protected $_SQL;
	protected $_SQLType;
	protected $_tableName;
	protected $_columns;
	protected $_conditions;

	public function __construct($SQLType="SELECT") {
		$this->_SQLType = $SQLType;
	}

	public function addWhere($whereCondition) {
		//USAGE:
		/*
		SQLBuilder1.addWhere("my_number=6");
		SQLBuilder1.addWhere("my_string='abc'");
		*/
		$this->_conditions[count($this->_conditions)] = $whereCondition;
	}

	public function addVarColumns($vars, $varMethod="POST") {
		//Maps $_POST or $_GET variables to columns of the same name
		//USAGE: 
		//$columns = array('size','color','quantity')
		//addVarColumns($columns,"GET");
		$arrlength = count($vars);
		
		for($x = 0; $x < $arrlength; $x++) {
			
			if (strtoupper($varMethod)=="GET") {
				if (isset($_GET[$vars[$x]])) {
					if($_GET[$vars[$x]]!="") $this->addColumn($vars[$x],$_GET[$vars[$x]]);
				} else {
					debug ("clsSQLBuilder:addVarColumns: empty variable GET['" . $vars[$x] . "']");
				}
			} else {
				if (isset($_POST[$vars[$x]])) {
					if($_POST[$vars[$x]]!="") $this->addColumn($vars[$x],$_POST[$vars[$x]]);
				} else { 
					debug ("clsSQLBuilder:addVarColumns: empty variable POST['" . $vars[$x] . "']");
				}
			}	

		}
		
	}

	public function addNullColumn($colName) {
		$elementNumber=count($this->_columns);
		$this->_columns[$elementNumber]['name'] = $colName;
		$this->_columns[$elementNumber]['value'] = 'null';
	}
	
	public function addColumn($colName, $colValue="", $strict=false) {
		//USAGE:
		/*
		
		//column for select  
		SQLBuilder1.addColumn("column_name");	
		//(Number) column for insert/update
		SQLBuilder1.addColumn("column_name", "6");
		//column for select with alias
		SQLBuilder1.addColumn("column_name", "column_alias");
		//NOTE: Use url encoding for multi-word aliases
		SQLBuilder1.addColumn("column_name", "column%20alias");
		
		
		*/
		
		$numeric=false;
		//check if decimal number or integer
		//$test = str_replace('.', '', $colValue);
		$test = preg_replace("/[^0-9.]/", "", $colValue);
		if ($test==$colValue AND strlen($colValue) > 0) {
			$numeric=true;
			//don't do this until after checking for illegal chars
			//$colValue = "'" . $colValue . "'";
		}
		//update 2020-09-09: allow null (for updates)
		if ($colValue=="null")	 $numeric=true	;
		
		//HTML encode quotation marks
		 $colValue = str_replace("'","&rsquo;",$colValue);
		 $colValue = str_replace('"',"&quot;",$colValue);
		
		//check for illegal characters
		//the following will be regarded as harmless
		$test = str_replace(' ', '', $colValue);
		$test = str_replace('.', '', $test);
		$test = str_replace(':', '', $test);
		$test = str_replace(',', '', $test);
		$test = str_replace('-', '', $test);
		$test = str_replace('_', '', $test);
		$test = str_replace('+', '', $test);		
		$test = str_replace('=', '', $test);				
		$test = str_replace('?', '', $test);
		$test = str_replace('!', '', $test);
		$test = str_replace('/', '', $test);
		$test = str_replace('$', '', $test);
		$test = str_replace('#', '', $test);
		$test = str_replace('@', '', $test);		
		$test = str_replace('*', '', $test);			
		$test = str_replace('(', '', $test);
		$test = str_replace(')', '', $test);		
		//for HTML encoding: '&quot;' etc.
		//the ";" could be trouble but as long as 
		// "'" is kept illegal, we're safe
		$test = str_replace('&', '', $test);
		$test = str_replace(';', '', $test);	
		//for URL encoding	
		$test = str_replace('%', '', $test);

		//$strict means characters must be explicitly allowed as in the lines above. 
		//Otherwise, we just reject single quotation marks  . . . which should already 
		//have been replaced above, but we want to be extra sure in any case
		$error_message="";
		if ($strict and !ctype_alnum($test)) {
			$error_message="(Strict mode)";
		}
		
		if ($colValue!=str_replace("'","",$colValue)) {
			$error_message='Single-quote mark not allowed here.'; 

		}
		
		if ($error_message!="") {
				$error_message .="clsSQLBuilder: Illegal character in test string: " . $test . " (length " . strlen($test) . ") for colValue (length " . strlen($colValue) . "):" . $colValue . " for column name: " . $colName . ' stripped value="' . str_replace("'","",$colValue) . '"';
				debug ($error_message);
				if ($test!="") die($error_message);
		}

		//Non-numeric input must be enclosed in single quotes both
		//for correct SQL parsing AND to prevent injection
		if(!$numeric) $colValue = "'" . $colValue . "'";
		
		$elementNumber=count($this->_columns);
		$this->_columns[$elementNumber]['name'] = $colName;
		$this->_columns[$elementNumber]['value'] = $colValue;
	}

	public function setTableName($tableName) {
		$this->_tableName= $tableName;
	}
	
	public function getSQL() {
		$whereClause=true;
		switch ($this->_SQLType) {
				case "SELECT":
					$SQL = "SELECT ";
					for($x = 0; $x < count($this->_columns); $x++) {
						if ($x==0) $prefix=" "; else $prefix=" , ";
						$SQL = $SQL . $prefix . $this->_columns[$x]['name'] . " ";
						if ($this->_columns[$x]['value']<>"") {
								$SQL = $SQL . " AS " . $this->_columns[$x]['value'] . " ";
						}
					}
					$SQL = $SQL . " FROM " . $this->_tableName . " " ;
					break;
				case "INSERT":
					$whereClause = false;
					$SQL = "INSERT INTO " . $this->_tableName . " (" ;
  					for($x = 0; $x < count($this->_columns); $x++) {
						if ($x==0) $prefix=" "; else $prefix=" , ";
						$SQL = $SQL . $prefix . $this->_columns[$x]['name'] . " ";
					}
					$SQL = $SQL . ") VALUES (";
					for($x = 0; $x < count($this->_columns); $x++) {
						if ($x==0) $prefix=" "; else $prefix=" , ";
						//watch data type?
						$SQL = $SQL . $prefix . $this->_columns[$x]['value'] . " ";
					}
					$SQL = $SQL . ")";
					break;
				case "UPDATE":
					$SQL = "UPDATE ". $this->_tableName . " SET " ;
					for($x = 0; $x < count($this->_columns); $x++) {
						if ($x==0) $prefix=" "; else $prefix=" , ";
						//watch data type?
						$SQL = $SQL . $prefix . $this->_columns[$x]['name'] . "=" . $this->_columns[$x]['value'] . " ";
					}					
					break;
				case "DELETE":
					$SQL = "DELETE FROM ". $this->_tableName . " " ;
					break;
		}
		if ($whereClause) {
			for($x = 0; $x < count($this->_conditions); $x++) {
				if ($x==0) $prefix=" WHERE "; else $prefix=" AND ";
				$SQL = $SQL . $prefix . $this->_conditions[$x] . " ";
			}
		}
		return $SQL;
	}
	
}

?>