<?php
    //defaults
    if (!isset($oldValue)) $oldValue="";
    if (!isset($inputPattern)) $inputPattern="[a-zA-Z0-9_]*";
    if (!isset($maxLength)) $maxLength=99;
    if (!isset($inputLength)) $inputLength=30;
    $inputPattern2="[a-zA-Z][a-zA-Z0-9@]*";
?>
    
    <?php 
		if ($action!="edit") {
			echo '<form action="admin.php?p=Users" method="post">
			<h3>New User</h3>
			<input type="hidden" id="flow" name="flow" value="insert">';
			$buttonName="Add";
		} else {
			echo '<form action="admin.php?p=Users&action=update" method="post">
			<h3>Edit User</h3>
			<input type="hidden" id="flow" name="flow" value="update">
			<input type="hidden" id="id" name="id" value="'.  $userID . '">';
			$sql="select * from user where id=" . $userID;
			$dds->setSQL($sql);
			$userrec=$dds->getNextRow("assoc");
			$address=$userrec['email'];
			$username=$userrec['username'];
			$buttonName="Save";
		}
	?>
    Name (alphanumeric/underscore): <input type="text" id="name" name="username" pattern="<?php echo $inputPattern; ?>" 
        size="20" maxlength="<?php echo $maxLength ?>" value="<?php echo $username; ?>"><br>
    e-mail: <input type="email" id="address" name="email" 
        size="<?php echo $inputLength ?>" maxlength="<?php echo $maxLength ?>" value="<?php echo $address; ?>"><br>
    <input type="submit" value="Submit">
    </form><br><br>
