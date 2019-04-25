<?php

/*
Usage is a five-step process
1. The table is constructed with arrays of field names and data types.
2. (optional) The table can be further defined (defineRows) by setting visibility and links for each field.
3. start() adds the <table> tag and sets up the header row.
4. addRow() takes an array of column values.
5. finish() adds the </table> tag.


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