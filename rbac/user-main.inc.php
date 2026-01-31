<?php

$sql="select id, username, email from user where id = " . $userID ;
	
$result = $dds->setSQL($sql) ;
$editableColumns[0]='username';
$editableColumns[1]='email';
function eB ($columnName) {
    global $editableColumns;
    global $userID;
        for ($i = 0; $i < count($editableColumns); $i++) {
                if($editableColumns[$i]==$columnName) {
                    echo ' <a href="admin.php?p=user&id=' . $userID . '&action=edit&column='. $columnName . '"><img height="20" src="Hydrogen/images/edit/edit.png"></a>';  
                }
        }

}

while ($rrow = $dds->getNextRow()) {
    $ID=$rrow[0];
    $userName=$rrow[1];
    $eMail=$rrow[2];
}

?>

    <br><br><h1><?php echo $userName; ?></h1>

      <table class="rbac_table">
        <tbody>
          <tr><td>ID</td><td><?php echo $ID; ?></td></tr>
          <tr><td>Username </td><td><?php echo $userName;  eB("username");  ?></td></tr>
          <tr><td>email</td><td><?php echo $eMail; eB("email");  ?></td></tr>

        </tbody>
      </table>

<?php
echo '<p><a href="admin.php?p=Users&action=delete&id='. $ID . '"><img class="button" src="Hydrogen/images/edit/remove.jpg"></a> Delete <p>';
include ("Hydrogen/rbac/user-roles.inc.php");
?>