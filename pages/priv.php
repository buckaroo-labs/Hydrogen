<?php
if(!$user_is_admin) {
  echo '<p>Your account lacks the administrative privilege necessary to use this page.</p>';
} else {
$privID=1;
if ( isset( $_GET['id'])) {
  if (is_numeric($_GET['id'])) $privID= $_GET['id'];
}
if ( isset( $_POST['id'])) {
  if (is_numeric($_POST['id'])) $privID= $_POST['id'];
}

$action="view";
if ( isset( $_POST['flow'])) {
  //accept new data and show updated record
	if ($_POST['flow']=="update") $action="update";
}
if ( isset( $_GET['action'])) {
  //show the edit form
  if ($_GET['action']=="edit") $action="edit";
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
  if ( isset($column) and isset($privID) and isset( $_POST['new_value'])) {

    //validate and perform the update, then show the results (change the action to "view")
    $newValue=sanitizePostVar('new_value');

    //$conn=new mysqli($settings['DEFAULT_DB_HOST'], $settings['DEFAULT_DB_USER'] , $settings['DEFAULT_DB_PASS'], $settings['DEFAULT_DB_INST']);
    $sql="update " . $tableName . " set " . $column . "=? where " . $keyName . "=?";
    $stmt=$conn->prepare($sql); 
    if ( false===$stmt )         die('prepare() failed for SQL ' . $sql . ': ' . htmlspecialchars($conn->error));
    $rc=$stmt->bind_param("si", $newValue, $privID);  
    if ( false===$rc )         die('bind_param() failed: ' . htmlspecialchars($stmt->error));
    $stmt->execute();
    $rowCount=$stmt->affected_rows;
    if ($rowCount==-1) {
      echo "<br><br><h2>Oops!</h2><p>There was a problem processing your update for ID " . $personID . ": '" . $sql . "'<br>" . $rowCount . " rows updated.
      <br>New value: " . $newValue . "</p><p>Eror: " . $stmt->error . " </p>  </div></div>";
      include "Hydrogen/elements/Navbar.php"; 
      include "Hydrogen/elements/Footer.php"; 
      echo ('</body></html>');
      die();
    } 
    $action="view";
  }
}

if ($action=="view") include ("Hydrogen/rbac/priv-main.inc.php");

}