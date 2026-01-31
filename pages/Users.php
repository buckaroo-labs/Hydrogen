<?php
require_once("Hydrogen/db/clsSQLBuilder.php");

if(!$user_is_admin) {
  echo '<p>Your account lacks the administrative privilege necessary to use this page.</p>';
} else {
		$userID=0;
	if (isset($_GET['id']) && is_numeric($_GET['id'])) $userID=$_GET['id'];
	if (isset($_POST['id']) && is_numeric($_POST['id'])) $userID=$_POST['id'];
	$action="view";
    if ( isset( $_GET['action'])) {
      if ($_GET['action']=="add") {
        $action="add";
      }
	  if ($_GET['action']=="delete") {
        $action="delete";
      }	
	  if ($_GET['action']=="update") {
        $action="update";
      }	
    }
    if ( isset( $_POST['flow'])) {
      if ($_POST['flow']=="insert") $action= $_POST['flow'];
    }
    //echo "<p>Action=</p>" . $action;
    if ($action=="add") {
      require ("Hydrogen/rbac/user-add.inc.php"); 
    }  
	if ($action=="delete" && $userID>0 ) { 
		$sql="delete from user where id=" . $userID;
		$dds->setSQL($sql);
		$action="view";
	}
	if ($action=="update" && $userID>0 ) { 
		//$sql="update user where id=" . $userID;
		$sb=new SQLBuilder("UPDATE");
		$sb->setTableName('user');
		$sb->addWhere("id=" . $userID);
		$columns = array('username','email','first_name','last_name');
		$sb->addVarColumns($columns,"POST");
		$dds->setSQL($sb->getSQL());
		$action="view";
	}
    if ($action=="insert") {
      if ($user_is_admin  and isset($_POST['username']) and isset($_POST['email']))    {
        //insert the new record and show the results
        $userName=sanitizePostVar('username');
        $userMail=sanitizePostVar('email');
        $sql="insert into user (username,email) values ('".$userName."','".$userMail."')";
        $dds->setSQL($sql);
        //$rowCount=$stmt->affected_rows;
        $rowCount=1;
        if ($rowCount==-1) {
          echo "<br><br><h2>Oops!</h2><p>There was a problem adding your record.<p> <br><br>";
        } 
      }
    } 

echo '      <p>
        The table below lists registered application users.
      </p>';
	  if($action=="view") echo '<a href="admin.php?p=Users&action=add"><img class="button" src="Hydrogen/images/edit/add.jpg"> Add</a>'; 
 echo '     <table class="sortable rbac_table">
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