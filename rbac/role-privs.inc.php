<?php

  $targetPage="admin.php?p=role";
  $leftHandImage="key.png";
  $nextAction="";
  $i=0;
  $iterations=2;
  $headerAdditionalText="";
  if ($action=="privs")  {
     $nextAction="&action=demappriv";
     $headerAdditionalText=" in role";
     $leftHandImage="edit/remove.jpg";
     //$targetPage="admin.php?p=role";
     $maybeNot="";
     $iterations=2;
  }

  while ($i < $iterations) {

    $i=$i+1;
    if ($i==2) {
      $nextAction="&action=mappriv";
      $leftHandImage="edit/add.jpg";
      $headerAdditionalText=" not in role";
      $maybeNot="not";
    } 

      $sql="select id, name, description from  privilege
      where id " . $maybeNot . " in (
        select privilege_id from m_role_privilege where role_id  = " . $roleID . ")";		
      $result = $dds->setSQL($sql) ;
      $has_records=false;
      $header_printed=false;
      while ($rrow = $dds->getNextRow()) {

        $has_records=true;
        if (!$header_printed) {

          echo '<h4>Privileges' . $headerAdditionalText . ':  ';
          if ($user_is_admin and $action=="view") echo '<a href="admin.php?p=role&id=' . $roleID . '&action=privs"><img class="button" src="Hydrogen/images/edit/dataentry.png"> Manage</a>';
          echo '</h4>
          <table>
          <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Description</th>
              </tr>';
          $header_printed=true;


        } 
        $additionalGet="";
        $keyID=$rrow[0];
        if ($nextAction!="") {
          $additionalGet=$nextAction . "&privid=" . $rrow[0];
          $keyID=$roleID;
        }
        echo '<tr>
        <td><a href="' . $targetPage . '&id=' . $keyID . $additionalGet . '"><img class="button" src="Hydrogen/images/'. $leftHandImage .'"></a></td>
        <td>' . $rrow[1] . '</td>
        <td>' . $rrow[2] . '</td>
        </tr>';


      }
      if ($has_records) echo "</table>";
  }

?>