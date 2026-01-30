<?php

$sql="select id, name, description from privilege where id = " . $privID ;
	
$result = $dds->setSQL($sql) ;

function eB ($columnName) {
    global $editableColumns;
    global $privID;

        for ($i = 0; $i < count($editableColumns); $i++) {
                if($editableColumns[$i]==$columnName) {
                    echo '<a href="admin.php?p=priv&id=' . $privID . '&action=edit&column='. $columnName . '"><img height="20" src="Hydrogen/images/edit/edit.png"></a>';  
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

    <br><br><h1>Privilege: <?php echo $Name; ?></h1>

      <table class="rbac_table">
        <tbody>
          <tr><td>ID</td><td><?php echo $ID; ?></td><td></td></tr>
          <tr><td>Name </td><td><?php echo $Name; ?></td><td><?php eB("username")  ?></td></tr>
          <tr><td>Description</td><td><?php echo $Description; ?></td><td><?php eB("email")  ?></td></tr>

        </tbody>
      </table>

<?php

include ("Hydrogen/rbac/priv-roles.inc.php");
?>