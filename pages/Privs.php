<?php 
if(!$user_is_admin) {
  echo '<p>Your account lacks the administrative privilege necessary to use this page.</p>';
} else {
	/* The "action" determines the page contents, behavior and purpose:
	1. "view" (default): list the defined privileges 
	2. "add" : present the form for adding a named privilege
	3. "insert" : process the submitted data for a new privilege and then proceed to "view"
	4. "delete" : process the submitted ID for deletion and then proceed to "view"
	*/
	$privID=0;
	if (isset($_GET['id']) && is_numeric($_GET['id'])) $privID=$_GET['id'];
	if (isset($_POST['id']) && is_numeric($_POST['id'])) $privID=$_POST['id'];
    $action="view";
    if ( isset( $_GET['action'])) {
      if ($_GET['action']=="add") {
        $action="add";
      }
      if ($_GET['action']=="delete") {
        $action="delete";
      }	  
    }
    if ( isset( $_POST['flow'])) {
      if ($_POST['flow']=="insert") $action= $_POST['flow'];
    }
    //echo "<p>Action=</p>" . $action;
    if ($action=="add") {
      require ("Hydrogen/rbac/priv-add.inc.php"); 
    }  
	if ($action=="delete" && $privID>0 ) { 
		$sql="delete from privilege where id=" . $privID;
		$dds->setSQL($sql);
		$action="view";
	}
    if ($action=="insert") {
      if (isset($_POST['privname']) and isset($_POST['pdescription']))    {
        //insert the new record and show the results
        $privName=sanitizePostVar('privname');
        $privDesc=sanitizePostVar('pdescription');
        $sql="insert into privilege (name,description) values ('".$privName."','".$privDesc."')";
        $dds->setSQL($sql);
        //$rowCount=$stmt->affected_rows;
        $rowCount=1;
        if ($rowCount!=1) {
          echo "<br><br><h2>Oops!</h2><p>There was a problem adding your record.<p> <br><br>";
        } 
		$action="view";
      }
    }  


echo '      <p>
        The table below lists application privileges.
        ';
if($action=="view") echo '<br><a href="admin.php?p=Privs&action=add"><img class="button" src="Hydrogen/images/edit/add.jpg"> </a> Add';
echo '
      </p>
      <table class="sortable rbac_table">
        <tbody>
        ';

$sql="select id, name, description from privilege";		
$result = $dds->setSQL($sql) ;


echo '<tr>';
echo '<th>Link</th>';
echo '<th>Name</th>';
echo '<th>Description</th> 
</tr>';
while ($rrow = $dds->getNextRow()) {
    //echo '<a name="'. $rrow[0] . '"></a>';
    echo "<tr>";
        echo '<td><a href="admin.php?p=priv&id=' . $rrow[0] . '"><img height="20" src="Hydrogen/images/key.png"></td><td>' . $rrow[1] . '</td><td>' , $rrow[2] . "</td>";
		echo "</tr>
    ";
	}

echo '       
        </tbody>
      </table>
      ';
} 
