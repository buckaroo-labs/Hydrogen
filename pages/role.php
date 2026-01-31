<?php
if(!$user_is_admin) {
  echo '<p>Your account lacks the administrative privilege necessary to use this page.</p>';
} else {
$roleID=0;
if ( isset( $_GET['id'])) {
  if (is_numeric($_GET['id'])) $roleID= $_GET['id'];
}
if ( isset( $_POST['id'])) {
  if (is_numeric($_POST['id'])) $roleID= $_POST['id'];
}

$action="view";
if ( isset( $_POST['flow'])) {
  //accept new data and show updated record
	if ($_POST['flow']=="update") $action="update";

  if ($_POST['flow']=="insert") $action="insert";

}
if ( isset( $_GET['action'])) {
  //show the edit form
  if ($_GET['action']=="edit") $action="edit";
  //show the add form
  if ($_GET['action']=="add") $action="add";
  //manage user mapping
  if ($_GET['action']=="users") $action="users";
  //manage priv mapping
  if ($_GET['action']=="privs") $action="privs";
  //map user to role
  if ($_GET['action']=="mapuser") $action="mapuser";
  //map priv to role
  if ($_GET['action']=="mappriv") $action="mappriv";
  //unmap user from role
  if ($_GET['action']=="demapuser") $action="demapuser";
  //unmap priv from role
  if ($_GET['action']=="demappriv") $action="demappriv";

}

if ($action=="edit") {
  if ( isset( $_GET['column'])) {
    for ($i = 0; $i < count($editableColumns); $i++) {
      if($editableColumns[$i]==$_GET['column']) $column=$_GET['column'];
    }
  }
}

if ($action=="update") {
  if ( isset( $_POST['column'])) {
    for ($i = 0; $i < count($editableColumns); $i++) {
      if($editableColumns[$i]==$_POST['column']) $column=$_POST['column'];
    }
  }
  if ( isset($column) and isset($personID) and isset( $_POST['new_value'])) {

    //validate and perform the update, then show the results (change the action to "view")
    $newValue=sanitizePostVar('new_value');

    $sql="update " . $tableName . " set " . $column . "=? where " . $keyName . "=?";
    //$stmt=$appdb->prepare($sql); 
    //if ( false===$stmt )         die('prepare() failed for SQL ' . $sql . ': ' . htmlspecialchars($conn->error));
    //$rc=$stmt->bind_param("si", $newValue, $roleID);  
    //if ( false===$rc )         die('bind_param() failed: ' . htmlspecialchars($stmt->error));
    //$stmt->execute();
    //$rowCount=$stmt->affected_rows;
	$sql="update " . $tableName . " set " . $column . "='".$newValue."' where " . $keyName . "=". $roleID;
	$appdb->setSQL($sql);
	$rowCount=1;
    if ($rowCount==-1) {
      echo "<br><br><h2>Oops!</h2><p>There was a problem processing your update for ID " . $roleID . ": '" . $sql . "'<br>" . $rowCount . " rows updated.
      <br>New value: " . $newValue . "</p><p>Eror: " . $stmt->error . " </p>  </div></div>";
      include "Hydrogen/elements/Navbar.php"; 
      include "Hydrogen/elements/Footer.php"; 
      echo ('</body></html>');
      die();
    } 

    $action="view";
  }

}

if ($action=="mapuser") {
  //process the change and show the results
  if ( isset( $_GET['userid'])) {
    if (is_numeric($_GET['userid'])) $userID= $_GET['userid'];
  }
  if ( isset($roleID) and isset($userID)  ) {
    //$conn=new mysqli($settings['DEFAULT_DB_HOST'], $settings['DEFAULT_DB_USER'] , $settings['DEFAULT_DB_PASS'], $settings['DEFAULT_DB_INST']);
    $sql="insert ignore into m_user_role (role_id,user_id) values (?,?)";
    $stmt=$conn->prepare($sql); 
    if ( false===$stmt )         die('prepare() failed for SQL ' . $sql . ': ' . htmlspecialchars($conn->error));
    $rc=$stmt->bind_param("ii", $roleID, $userID);  
    if ( false===$rc )         die('bind_param() failed: ' . htmlspecialchars($stmt->error));
    $stmt->execute();
  }

  $action="users";
}

if ($action=="demapuser") {
  //process the change and show the results

  if ( isset( $_GET['userid'])) {
    if (is_numeric($_GET['userid'])) $userID= $_GET['userid'];
  }
  if ( isset($roleID) and isset($userID)  ) {


    //$conn=new mysqli($settings['DEFAULT_DB_HOST'], $settings['DEFAULT_DB_USER'] , $settings['DEFAULT_DB_PASS'], $settings['DEFAULT_DB_INST']);
    $sql="delete from m_user_role where role_id=? and user_id=?";
    $stmt=$conn->prepare($sql); 
    if ( false===$stmt )         die('prepare() failed for SQL ' . $sql . ': ' . htmlspecialchars($conn->error));
    $rc=$stmt->bind_param("ii", $roleID, $userID);  
    if ( false===$rc )         die('bind_param() failed: ' . htmlspecialchars($stmt->error));
    $stmt->execute();

  }

  $action="users";
}

if ($action=="mappriv") {
  //process the change and show the results
  if ( isset( $_GET['privid'])) {
    if (is_numeric($_GET['privid'])) $privID= $_GET['privid'];
  }
  if ( isset($roleID) and isset($privID)  ) {

    //$conn=new mysqli($settings['DEFAULT_DB_HOST'], $settings['DEFAULT_DB_USER'] , $settings['DEFAULT_DB_PASS'], $settings['DEFAULT_DB_INST']);
    $sql="insert ignore into m_role_privilege (role_id,privilege_id) values (?,?)";
    $stmt=$conn->prepare($sql); 
    if ( false===$stmt )         die('prepare() failed for SQL ' . $sql . ': ' . htmlspecialchars($conn->error));
    $rc=$stmt->bind_param("ii", $roleID, $privID);  
    if ( false===$rc )         die('bind_param() failed: ' . htmlspecialchars($stmt->error));
    $stmt->execute();

  }

  $action="privs";
}

if ($action=="demappriv") {
  //process the change and show the results

  if ( isset( $_GET['privid'])) {
    if (is_numeric($_GET['privid'])) $privID= $_GET['privid'];
  }
  if ( isset($roleID) and isset($privID)  ) {


    //$conn=new mysqli($settings['DEFAULT_DB_HOST'], $settings['DEFAULT_DB_USER'] , $settings['DEFAULT_DB_PASS'], $settings['DEFAULT_DB_INST']);
    $sql="delete from m_role_privilege where role_id=? and privilege_id=?";
    $stmt=$conn->prepare($sql); 
    if ( false===$stmt )         die('prepare() failed for SQL ' . $sql . ': ' . htmlspecialchars($conn->error));
    $rc=$stmt->bind_param("ii", $roleID, $privID);  
    if ( false===$rc )         die('bind_param() failed: ' . htmlspecialchars($stmt->error));
    $stmt->execute();
  }

  $action="privs";
}

if ($action=="users") include ("Hydrogen/rbac/role-main.inc.php");
if ($action=="privs") include ("Hydrogen/rbac/role-main.inc.php");
if ($action=="view" ) include ("Hydrogen/rbac/role-main.inc.php");
}