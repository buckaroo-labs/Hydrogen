<?php
$pagetitle="Hydrogen Setup";
$headline = '<h1>Setup</h1>' ;
$setup_mode=true;
include "Hydrogen/pgTemplate.php";

if (isset($_GET['setmeup'])) {
  $secret = '<?php $' . "settings['JWT-SECRET-KEY']='" . md5(uniqid()) . "'; ?>";
  $secretsfile = file_put_contents('settingsPasswords.php', $secret.PHP_EOL , FILE_APPEND | LOCK_EX);
}
@include('settingsPasswords.php');
if (empty($settings['JWT-SECRET-KEY'])) {
  $greeting='<p>The Hydrogen library does not detect a secret key in your configuration files. 
  Click the <q>Setup</q> button below to add an auto-generated JWT secret key to the file <code>
  settingsPasswords.php</code> in your application root, or add one yourself and return to this page if necessary to confirm.</p>
  ';
  $greeting.='<p>Example: <code>$settings[' . "'" . "JWT-SECRET-KEY']=" . '"uvaikmu5ctbggczovgzpk5hgdgow";</code></p><br>
  <form>
    <input type="hidden" name="setmeup" value="1">
    <input type="submit" value="Submit">
  </form> 
  ';
} else {
  $greeting="<p>You're all set. Enjoy!</p>";
}
?>

<!-- Main content: shift it to the right when the sidebar is visible -->
<div class="w3-main">

<?php include 'Hydrogen/elemLogoHeadline.php';  	 ?>

  <div class="w3-row w3-padding-64">
    <div class="w3-twothird w3-container">
      <?php echo $greeting; ?>
    </div>

  </div>



<!-- END MAIN -->
</div>

<?php
	//Yes, it goes at the top, but it may use variables (session status) that are set by what happens in the middle -
	//   so include it at the end and then let it float to the top
	include 'Hydrogen/elemNavbar.php';
	include "Hydrogen/elemFooter.php";
?>
</body></html>