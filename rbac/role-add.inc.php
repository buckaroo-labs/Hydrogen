<?php
    //defaults
    if (!isset($oldValue)) $oldValue="";
    if (!isset($inputPattern)) $inputPattern="[a-zA-Z0-9_]*";
    if (!isset($maxLength)) $maxLength=99;
    if (!isset($inputLength)) $inputLength=30;
    $inputPattern2="[a-zA-Z0-9 ]*";
?>
    <form action="admin.php?p=Roles" method="post">
    <!-- these would be for an edit
    <input type="hidden" id="id" name="id" value="<?php echo $roleID; ?>">
    <input type="hidden" id="column" name="column" value="<?php echo $column; ?>">
    -->
    <h3>New role</h3>
    <input type="hidden" id="flow" name="flow" value="insert">
    Name (alphanumeric/underscore): <input type="text" id="rolename" name="rolename" pattern="<?php echo $inputPattern; ?>" 
        size="20" maxlength="<?php echo $maxLength ?>" value="<?php echo $oldValue; ?>"><br>
    Description (alphanumeric/space): <input type="text" id="rdescription" name="rdescription" pattern="<?php echo $inputPattern2; ?>" 
        size="<?php echo $inputLength ?>" maxlength="<?php echo $maxLength ?>" value="<?php echo $oldValue; ?>"><br>
    <input type="submit" value="Add">
    </form><br><br>
