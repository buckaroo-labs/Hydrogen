<?php

$sql="select id, name, description from role 
where id in (
  select role_id from m_role_privilege where privilege_id  = " . $privID . ")";		
$result = $dds->setSQL($sql) ;
$has_records=false;
$header_printed=false;
while ($rrow = $dds->getNextRow()) {

  $has_records=true;
  if (!$header_printed) {
    echo '<h4>Roles:</h4>
    <table>
    <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Description</th>
        </tr>';
    $header_printed=true;


  } 
  echo '<tr>
  <td><a href="role.php?id=' . $rrow[0] . '"><img height="20" src="Hydrogen/images/socnet.png"></a></td>
  <td>' . $rrow[1] . '</td>
  <td>' . $rrow[2] . '</td>
  </tr>';


}
if ($has_records) echo "</table>";

?>