<?php 
if(!$user_is_admin) {
  echo '<p>Your account lacks the administrative privilege necessary to use this page.</p>';
} else {
    $action="view";
    if ( isset( $_GET['action'])) {
      if ($_GET['action']=="add") {
        $action="add";
        //echo "<p>GET-Action=Add</p>";
      }
    }
    if ( isset( $_POST['flow'])) {
      if ($_POST['flow']=="insert") $action= $_POST['flow'];
    }
    //echo "<p>Action=</p>" . $action;
    if ($action=="add") {
      require ("Hydrogen/rbac/priv-add.inc.php"); 
    }  

    if ($action=="insert") {
      if ($user_is_admin  and isset($_POST['privname']) and isset($_POST['pdescription']))    {
        //insert the new record and show the results
        $privName=sanitizePostVar('privname');
        $privDesc=sanitizePostVar('pdescription');

        //$conn=new mysqli($settings['DEFAULT_DB_HOST'], $settings['DEFAULT_DB_USER'] , $settings['DEFAULT_DB_PASS'], $settings['DEFAULT_DB_INST']);
        $sql="insert into privilege (name,description) values ('".$privName."','".$privDesc."')";
        //$stmt=$dds->prepare($sql); 
        //if ( false===$stmt )         die('prepare() failed for SQL ' . $sql . ': ' . htmlspecialchars($conn->error));
        //$rc=$stmt->bind_param("ss", $privName, $privDesc);  
        //if ( false===$rc )         die('bind_param() failed: ' . htmlspecialchars($stmt->error));
        //$stmt->execute();
        $dds->setSQL($sql);
        //$rowCount=$stmt->affected_rows;
        $rowCount=1;
        if ($rowCount==-1) {
          echo "<br><br><h2>Oops!</h2><p>There was a problem adding your record.<p> <br><br>";
        } 
      }
    }  


echo '      <p>
        The table below lists application privileges.
        ';
if($action=="view") echo '<a href="admin.php?p=Privs&action=add"><img height="20" src="Hydrogen/images/edit/add.jpg"> Add</a>';
echo '
      </p>
      <table class="sortable rbac_table">
        <tbody>
        ';

$sql="select id, name, description from privilege";		
$result = $dds->setSQL($sql) ;


echo '<tr>';
echo '<th>ID</th>';
echo '<th>Name</th>';
echo '<th>Description</th> 
</tr>';
while ($rrow = $dds->getNextRow()) {
    //echo '<a name="'. $rrow[0] . '"></a>';
    echo "<tr>";
        echo '<td><a href="admin.php?p=priv&?id=' . $rrow[0] . '"><img height="20" src="Hydrogen/images/key.png"></td><td>' . $rrow[1] . '</td><td>' , $rrow[2] . "</td>";
		echo "</tr>
    ";
	}

echo '       
        </tbody>
      </table>
      ';
} 
