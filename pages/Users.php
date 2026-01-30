<?php
if(!$user_is_admin) {
  echo '<p>Your account lacks the administrative privilege necessary to use this page.</p>';
} else {
echo '      <p>
        The table below lists registered application users.
      </p>
      <table class="sortable rbac_table">
        <tbody>
        ';
    


$sql="select id, username, email, last_login from user";		
$result = $dds->setSQL($sql) ;


echo '<tr>';
echo '<th>ID</th>';
echo '<th>Username</th>';
echo '<th>Email</th><th>Last login</th>
</tr>';
while ($rrow = $dds->getNextRow()) {
    //echo '<a name="'. $rrow[0] . '"></a>';
    echo "<tr>";
        echo '<td><a href="admin.php?p=user&id=' . $rrow[0] . '"><img height="20" src="Hydrogen/images/account.png"></td><td>' . $rrow[1] . '</td><td>' . $rrow[2] . '</td><td>' , $rrow[3] . "</td>";
		echo "</tr>";
	}

echo '        </tbody>
      </table>
';
}