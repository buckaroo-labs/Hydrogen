<?php

if (isset($_SESSION['username'])) {
    $privileges=array();  
    $sql ="SELECT p.name
    FROM user u inner join `m_user_role` mur on u.id=mur.user_id
    inner join m_role_privilege mrp on mur.role_id=mrp.role_id
    inner join privilege p on p.id=mrp.privilege_id
    WHERE u.username ='" . $_SESSION['username'] . "'";
    $result=$dds->setSQL($sql);
    while ($rrow=$dds->getNextRow()) {
        $privileges[$rrow[0]]="true";
    }
    $sql ="SELECT r.name
    FROM user u inner join `m_user_role` mur on u.id=mur.user_id
    inner join role r on r.id=mur.role_id
    WHERE u.username ='" . $_SESSION['username'] . "'";
    $result=$dds->setSQL($sql);
    while ($rrow=$dds->getNextRow()) {
        if ($rrow[0]=="admin") $user_is_admin=true;
    }
}

function active_priv($privName) {
    global $privileges;
    global $user_is_admin;
    $retVal=false;
    if (isset($privileges[$privName])) $retVal=true;
    if ($user_is_admin) $retVal=true;
    return $retVal;
}


?>