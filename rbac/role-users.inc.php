<?php

if ($user_is_admin) {
    $targetPage="admin.php?p=role";
    $leftHandImage="socnet.png";
    $nextAction="";
    $i=0;
    $iterations=2;
    $headerAdditionalText="";
    if ($action=="users")  {
       $nextAction="&action=demapuser";
       $headerAdditionalText=" in role";
       $leftHandImage="edit/remove.jpg";
       //$targetPage="admin.php?p=role";
       $maybeNot="";
       $iterations=2;
    }
    while ($i < $iterations) {
        $i=$i+1;
        if ($i==2) {
          $nextAction="&action=mapuser";
          $leftHandImage="edit/add.jpg";
          $headerAdditionalText=" not in role";
          $maybeNot="not";
        } 
        $sql="select id, username, email from user 
        where id " . $maybeNot . " in (
          select user_id from m_user_role where role_id  = " . $roleID . ")";		
        $result = $dds->setSQL($sql) ;
        $has_records=false;
        $header_printed=false;
        while ($rrow = $dds->getNextRow()) {

          $has_records=true;
          if (!$header_printed) {
            echo '<br><h4>Users' . $headerAdditionalText . ':  ';

            echo '</h4>';
			if ($user_is_admin and $action=="view") echo '<a href="admin.php?p=role&id=' . $roleID . '&action=users"><img class="button" src="Hydrogen/images/edit/dataentry.png"> Manage</a>';
			echo '
            <table>
            <tr>
            <th>ID</th>
            <th>Username</th>
            <th>email</th>
                </tr>';
            $header_printed=true;


          } 
          $additionalGet="";
          $keyID=$rrow[0];
          if ($nextAction!="") {
            $additionalGet=$nextAction . "&userid=" . $rrow[0];
            $keyID=$roleID;
          }
          echo '<tr><td><a href="' . $targetPage . '&id=' . $keyID . $additionalGet . '"><img class="button" src="Hydrogen/images/'. $leftHandImage .'"></a></td>
          <td>' . $rrow[1] . '</td>
          <td>' . $rrow[2] . '</td>
          </tr>';


        }
        if ($has_records) echo "</table>";
    }
}
?>