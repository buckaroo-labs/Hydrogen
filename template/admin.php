<?php
$pagetitle="Hydrogen Setup";
$headline = '<h1>Setup</h1>' ;
require_once("settingsHydrogen.php");
session_start();
//Most of this file deals with initial setup. The many other functions that 
//this page will serve are accomplished using includes.
if (array_key_exists('JWT-SECRET-KEY',$settings) && 
	array_key_exists('SQLITE-SECRET-KEY',$settings) && 
	array_key_exists('unauthenticated-app',$settings)
	) {
		if (array_key_exists('setup-mode',$_SESSION)) unset($_SESSION['setup_mode']) ;
	} else {
		//see pgTemplate.php
		$_SESSION['setup_mode']=true;
	} 
include "Hydrogen/pgTemplate.php";

if (isset($_GET['jwtsetup']) && !array_key_exists('JWT-SECRET-KEY',$settings)) {
  //phpinfo() will help add some randomness to the key.
  ob_start();
  phpinfo();
  $secret= md5(uniqid() . ob_get_clean());
  $output = "<?php\n" . 
"   //Changing the JWT secret key will invalidate any tokens 
   //issued by the application.\n" .
	'   $' . "settings['JWT-SECRET-KEY']='" .$secret . "';\n";
  $output .= "?>";
  $secretsfile = file_put_contents('settingsPasswords.php', $output.PHP_EOL , FILE_APPEND | LOCK_EX);
  
} elseif (isset($_GET['sqlsetup']) && !array_key_exists('SQLITE-SECRET-KEY',$settings)) {
  //phpinfo() will help add some randomness to the key.
  ob_start();
  phpinfo();
  $secret= md5(uniqid() . ob_get_clean());
  $output = "<?php\n";
	$output .="   //Changing the sqlite secret key will break the mapping
   //from the application to its data files. Use caution!\n";
	$output .=  '   $' . "settings['SQLITE-SECRET-KEY']='" . md5($secret) . "';\n";
  $output .= "?>";
  $secretsfile = file_put_contents('settingsPasswords.php', $output.PHP_EOL , FILE_APPEND | LOCK_EX);
} elseif (isset($_GET['authsetup']) && !array_key_exists('unauthenticated-app',$settings)) {
  if ($_GET['authsetup']==0 || $_GET['authsetup']==1 ) { 
    $output = "<?php\n"; 
	$unauthenticated=0;
	if ($_GET['authsetup']==0) $unauthenticated=1;
    //$_SESSION['unauthenticated-app']=$unauthenticated;
	$output .= '  $' . "settings['unauthenticated-app']='" . $unauthenticated . "';\n";
    if ($_GET['authsetup']==1 ) {
	  include_once("Hydrogen/db/clsDataSource.php");
      //Add a user to the DB and save the password 
      $adminname= 'u' . substr(md5(uniqid()),0,12);
      $adminpass= substr(md5($adminname . uniqid()),0,12);
      $adminhash= password_hash($adminpass,PASSWORD_BCRYPT);
      $sql="INSERT INTO USER (username, password_hash,email,first_name,last_name) VALUES ('" . $adminname . 
        "','" . $adminhash . "','noreply-" . uniqid() . "@example.com','Admin','User')";
      $dds->setSQL($sql); 
      $output .= '  $' . "settings['admin-name']='" . $adminname . "';\n";
      $output .= '  $' . "settings['admin-passwd']='" . $adminpass . "';\n";
	  //$output .= '// DEBUG: hashed passwd: ' . $adminhash . "';\n";
      //Also define some roles and privileges in the DB tables



      //lots more code here


    }
    $output .= "?>";
    $secretsfile = file_put_contents('settingsPasswords.php', $output.PHP_EOL , FILE_APPEND | LOCK_EX);
  } 

}


@include('settingsPasswords.php');

//set $greeting if minimum setup is not yet complete.
if (empty($settings['JWT-SECRET-KEY'])) {
  $greeting='<h1>Check 1 of 3</h1><br>
  <ul class="setupChecklist">
    <li><span style="color:red">X</span> JWT secret key</li>
    <li><span >?</span> Database secret key</li>
    <li><span >?</span> Admin account</li>
  </ul>
  <p>The Hydrogen library does not detect a JWT secret key in your configuration files. Click the <q>Setup</q> button below to add an auto-generated JWT secret key to the file <code>
  settingsPasswords.php</code> in your application root, or add one yourself and return to this page if necessary to confirm.</p>
  ';
  $greeting.='<p>Example: <code>$settings[' . "'" . "JWT-SECRET-KEY']=" . '"uvaikmu5ctbggczovgzpk5hgdgow";</code></p><br>
  <form>
    <input type="hidden" name="jwtsetup" value="1">
    <input type="submit" value="Setup">
  </form> 
  ';
} elseif (empty($settings['SQLITE-SECRET-KEY'])) {
  $greeting='<h1>Check 2 of 3</h1><br>
    <ul class="setupChecklist">
    <li><span style="color:green">&#10003;</span> JWT secret key</li>
    <li><span style="color:red">X</span> Database secret key</li>
    <li><span >?</span> Admin account</li>
  </ul>
  <p>The Hydrogen library does not detect a SQLite secret key in your configuration files, which will be necessary for securing data stored in SQLite databases (You can configure alternative databases in a later step). Click the <q>Setup</q> button below to add an auto-generated secret key to the file <code>settingsPasswords.php</code> in your application root, or add one yourself and return to this page if necessary to confirm.</p>
  ';
  $greeting.='<p>Example: <code>$settings[' . "'" . "SQLITE-SECRET-KEY']=" . '"uvaikmgzpk5hgu5ctbggczovdgow";</code></p><br>
  <form>
    <input type="hidden" name="sqlsetup" value="1">
    <input type="submit" value="Setup">
  </form> 
  ';
} else {
  //Check if there are users defined in the database or
  //if the setup determined to skip that step

  //more code here
  include_once('Hydrogen/db/clsDataSource.php');
  $sql="SELECT count(*) AS ucount FROM user";
  $dds->setSQL($sql);
  $rrow=$dds->getNextRow();

  if (isset($_SESSION['setup_mode']) && !isset($settings['unauthenticated-app']) && $rrow['ucount']==0) {
    $greeting='<h1>Check 3 of 3</h1><br>
      <ul class="setupChecklist">
      <li><span style="color:green">&#10003;</span> JWT secret key</li>
      <li><span style="color:green">&#10003;</span> Database secret key</li>
      <li><span >?</span> Admin account</li>
    </ul>
    <p>Would you like to implement authentication? Click
      the <q>Setup</q> button below to add an administrative user
      with a random name and password which you will be able to find in your <code>$settingsPasswords.php</code> file. Otherwise, click the <q>Cancel</q> button below.</p><br>
    <form>
      <input type="hidden" name="authsetup" value="1">
      <input type="submit" value="Setup">
    </form> 
    <form>
      <input type="hidden" name="authsetup" value="0">
      <input type="submit" value="Cancel">
    </form> ';
  }
}

if(isset($_GET['p']) && strcmp($_GET['p'],"UserAdmin")==0) $page=$_GET['p'];
if(isset($_GET['p']) && strcmp($_GET['p'],"RoleAdmin")==0) $page=$_GET['p'];
if(isset($_GET['p']) && strcmp($_GET['p'],"PrivAdmin")==0) $page=$_GET['p'];
if(isset($_GET['p']) && strcmp($_GET['p'],"Mail")==0) $page=$_GET['p'];
?>


<!-- Main content: shift it to the right when the sidebar is visible -->
<div class="w3-main">

<?php include 'Hydrogen/elements/LogoHeadline.php';  	 ?>

  <div class="w3-row w3-padding-64">
    <div class="w3-twothird w3-container">
      <?php 
        if(isset($greeting)) {
          echo $greeting; 
        } elseif(isset($page)) {
           include('Hydrogen/pages/' . $page . ".php"); 
        } else {
          echo '<p>Minimal Setup is complete. What would you like to do next?</p>';
          if (!isset($_SESSION['username'])) echo '<p>The following pages require you to be logged in:</p>';
		  //see https://github.com/buckaroo-labs/SabreDance/blob/main/index.php lines 184-189
          echo '<h4>Additional setup</h4><ul>
            <li><a href="admin.php?p=Mail">Mail</a> (required for user self-registration)</li>
            <li><a href="admin.php?p=MySQL">MySQL</a></li>
            <li><a href="admin.php?p=Logos">App branding</a></li>
          </ul>';
		  echo '<h4>Role-based Access Control</h4><ul>
            <li><a href="admin.php?p=UserAdmin">User administration</a></li>
            <li><a href="admin.php?p=RoleAdmin">Role administration</a></li>
            <li><a href="admin.php?p=PrivAdmin">Privilege administration</a></li>
          </ul>';
        }
      ?>
    </div>
  </div>

<!-- END MAIN -->
</div>

<?php
	//Yes, it goes at the top, but it may use variables (e.g. session status) that are set by what happens in the middle -
	//   so include it at the end and then let it float to the top
	include 'Hydrogen/elements/Navbar.php';
	include "Hydrogen/elements/Footer.php";
?>
</body></html>