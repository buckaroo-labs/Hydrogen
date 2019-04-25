<?php

/*
Usage is a five-step process
1. The table is constructed with arrays of field names and data types.
2. (optional) The table can be further defined (defineRows) by setting visibility and links for each field.
3. start() adds the <table> tag and sets up the header row.
4. addRow() takes an array of column values.
5. finish() adds the </table> tag.


EXAMPLE:
//Define a table based on the names and types of fields in a SQL query
//(See class file for dataSource class for more detail)
$table=new HTMLTable($dds->getFieldNames(),$dds->getFieldTypes());
unset($address_classes);
unset($linkURLs);
unset($linkTargets);
unset($keycols);
unset($invisible);

//Clicking on the first TD element in the row should send the user to page x, using a GET variable to be defined below
$linkURLs[0] = '/page_x.php?id=';
//Clicking on the next TD element in the row should send the user to page y, using a GET variable to be defined below
$linkURLs[1] = '/page_y.php?id=';
//Clicking on the next TD element in the row should send the user to the current page, using a GET variable to be defined below  
$linkURLs[2] = $_SERVER['SCRIPT_NAME'] .'?id=';

//Clicking on the first element in the row should open a new page
$linkTargets[0]="_blank";
//Clicking on the next element in the row should open a new page
$linkTargets[1]="_blank";

//If a $linkURL for the element is set, the default is to use each element's own data as the GET variable in the link
//But in the following case, the data in the second [1] element should be used as a GET variable for the link in the third [2] TD element
$keycols[2]=1;

//TD elements are visible by default
//In the following case, the second [1] TD element in the row should be invisible
$invisible[1]=1;

//Classes can be used by css or javascript to change the appearance of the link
//The address tag in the first element in the row should have a class of "class_x" 
$address_classes[0]='class_x';
//The address tag in the next element in the row should have a class of "class_y"
$address_classes[1]='class_y';

//And now we are ready to start generating the HTML for the table:
$table->defineRows($linkURLs,$keycols,$invisible,$address_classes,$linkTargets);
$table->start();
while ($result_row = $dds->getNextRow()){
	$table->addRow($result_row);
}
$table->finish();

*/

class HTMLTable {

	protected $rownum;
	protected $linkURLs;
	protected $a_classes;
	protected $keycols;
	protected $invisible;

	public function __construct($fieldNames,$fieldTypes) {
		$this->fieldNames=$fieldNames;
		$this->fieldTypes=$fieldTypes;
	}

	public function start() {
		echo ('<table class="sortable"><tr>');

		$arraylength=count($this->fieldNames);
		//echo ('<th>array length: '.$arraylength.'</th>');
		for ($field =0; $field < $arraylength; $field++) {
			//if ($this->invisible[$field]==0) {
				echo '<th';
				if (isset($this->fieldTypes[$field])) {
					switch ($this->fieldTypes[$field]) {
							case "xxx":
							echo ' class="sorttable_xxx"';
							break;
						default:
							echo ' class="sorttable_alpha"';
					}
				}
				echo '>'.$this->fieldNames[$field] . '</th>';
			//}
		}
		echo '</tr>';
		$rownum=1;
	}

	public function defineRows(
		$linkURLs,
		$keycols=array(),
		$invisible=array(),
		$a_classes=array(),
		$linkTargets=array()	){

		$fields=count($this->fieldNames);
		for ($i=0; $i <= $fields; $i++) {
			if (!isset($keycols[$i]))	{
				$keycols[$i]=$i;
			}
			if (!isset($invisible[$i]))	{
				$invisible[$i]=0;
			}
		}

		$this->linkURLs=$linkURLs;
		$this->linkTargets=$linkTargets;
		$this->keycols=$keycols;
		$this->invisible=$invisible;
		$this->a_classes=$a_classes;
	}


	public function addRow($rowdata) {
		$this->rownum++;
		echo '<tr';
		if ($this->rownum%2 == "1") {echo ' class="alt"';}
		echo '>';

		$arraylength=count($rowdata);
		debug("Arraylength: " . $arraylength);
		for ($field =0; $field < $arraylength; $field++) {
			debug("Field number: " . $field);
			if ($this->invisible[$field]==0) {
				echo '<td>';
				if (isset($this->linkURLs[$field])) {
					echo '<a href="' . $this->linkURLs[$field] . urlencode($rowdata[$this->keycols[$field]]) . '"';
					if (isset($this->a_classes[$field])) {
						echo ' class="' . $this->a_classes[$field] . '"';
					}
					if (isset($this->linkTargets[$field])) {
						echo ' target="' . $this->linkTargets[$field] . '"';
					}
					echo ">";
				}
				echo $rowdata[$field];
				if (isset($this->linkURLs[$field])) {
					echo '</a>';
				}
				echo '</td>';
			}
		}

		echo '</tr>';
	}

	public function finish() {
		echo '</table>';
	}

}

?>