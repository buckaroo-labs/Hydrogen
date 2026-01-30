<?php
if(!$user_is_admin) {
  echo '<p>Your account lacks the administrative privilege necessary to use this page.</p>';
} else {
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