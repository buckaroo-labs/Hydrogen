<?php
if(!$user_is_admin) {
  echo '<p>Your account lacks the administrative privilege necessary to use this page.</p>';
} else {
	$roleID=0;
	if (isset($_GET['id']) && is_numeric($_GET['id'])) $roleID=$_GET['id'];
	if (isset($_POST['id']) && is_numeric($_POST['id'])) $roleID=$_POST['id'];
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
      require ("Hydrogen/rbac/role-add.inc.php"); 
    }  
	if ($action=="delete" && $roleID>0 ) { 
		$sql="delete from role where id=" . $roleID;
		$dds->setSQL($sql);
		$action="view";
	}
    if ($action=="insert") {
      if ($user_is_admin  and isset($_POST['rolename']) and isset($_POST['rdescription']))    {
        //insert the new record and show the results
        $roleName=sanitizePostVar('rolename');
        $roleDesc=sanitizePostVar('rdescription');
        $sql="insert into role (name,description) values ('".$roleName."','".$roleDesc."')";
        $dds->setSQL($sql);
        //$rowCount=$stmt->affected_rows;
        $rowCount=1;
        if ($rowCount==-1) {
          echo "<br><br><h2>Oops!</h2><p>There was a problem adding your record.<p> <br><br>";
        } 
      }
    }  

  echo '<p>
        The table below lists application roles.
        

      </p>';
if($action=="view") echo '<a href="admin.php?p=Roles&action=add"><img height="20" src="Hydrogen/images/edit/add.jpg"> Add</a>';      
 echo '     <table class="sortable rbac_table">
        <tbody>
        ';

$sql="select id, name, description from role";		
$result = $dds->setSQL($sql) ;


echo '<tr>';
echo '<th>ID</th>';
echo '<th>Name</th>';
echo '<th>Description</th> 
</tr>';
while ($rrow = $dds->getNextRow()) {
    echo "<tr>";
        echo '<td><a href="admin.php?p=role&id=' . $rrow[0] . '"><img height="20" src="Hydrogen/images/socnet.png"></td><td>' . $rrow[1] . '</td><td>' , $rrow[2] . "</td>";
		echo "</tr>";
	}

echo '        </tbody>
      </table>
      ';
}