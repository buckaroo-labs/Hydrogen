<?php

$sql="select id, name, description from role where id = " . $roleID ;
	
$result = $dds->setSQL($sql) ;

function eB ($columnName) {
    global $editableColumns;
    global $roleID;
    

        for ($i = 0; $i < count($editableColumns); $i++) {
                if($editableColumns[$i]==$columnName) {
                    echo ' <a href="admin.php?p=role&id=' . $roleID . '&action=edit&column='. $columnName . '"><img height="20" src="Hydrogen/images/edit/edit.png"></a>';  
                }
        }



}

while ($rrow = $dds->getNextRow()) {
    //echo "<tr>";
    
    //person
    $ID=$rrow[0];
    $Name=$rrow[1];
    $Description=$rrow[2];
 
}

?>

    <br><br><h1>Role: <?php echo $Name; ?></h1>
    <?php echo $sqlResult; ?>
      <table class="rbac_table">
        <tbody>
          <tr><td>ID</td><td><?php echo $ID; ?></td></tr>
          <tr><td>Name </td><td><?php echo $Name; ?><?php eB("name")  ?></td></tr>
          <tr><td>Description</td><td><?php echo $Description; ?><?php eB("description")  ?></td></tr>

        </tbody>
      </table>

<?php
echo '<p><a href="admin.php?p=Roles&action=delete&id='. $ID . '"><img class="button" src="Hydrogen/images/edit/remove.jpg"></a> Delete <p>';
if ($action=="view") include ("Hydrogen/rbac/role-users.inc.php");
if ($action=="view") include ("Hydrogen/rbac/role-privs.inc.php");
if ($action=="users") include ("Hydrogen/rbac/role-users.inc.php");
if ($action=="privs") include ("Hydrogen/rbac/role-privs.inc.php");
?>