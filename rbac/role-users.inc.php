<?php

if ($user_is_admin) {
    $targetPage="user.php";
    $leftHandImage="socmed.png";
    $nextAction="";
    $i=0;
    $iterations=1;
    $headerAdditionalText="";
    if ($action=="users")  {
       $nextAction="&action=demapuser";
       $headerAdditionalText=" in role";
       $leftHandImage="remove.jpg";
       $targetPage="role.php";
       $maybeNot="";
       $iterations=2;
    }
    while ($i < $iterations) {
        $i=$i+1;
        if ($i==2) {
          $nextAction="&action=mapuser";
          $leftHandImage="add.jpg";
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
            echo '<h4>Users' . $headerAdditionalText . ':  ';
            if ($user_is_admin and $action=="view") echo '<a href="admin.php?p=role&id=' . $roleID . '&action=users"><img height="30" src="Hydrogen/images/edit/dataentry.png"> Manage</a>';
            echo '</h4>
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
          echo '<tr><td><a href="' . $targetPage . '?id=' . $keyID . $additionalGet . '"><img height="20" src="Hydrogen/images/'. $leftHandImage .'"></a></td>
          <td>' . $rrow[1] . '</td>
          <td>' . $rrow[2] . '</td>
          </tr>';


        }
        if ($has_records) echo "</table>";
    }
}
?>