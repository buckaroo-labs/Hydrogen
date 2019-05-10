<?php

// Define a registration page URL in your included settings file
// 		or page template; or use the default defined in settingsLogin.php.
// Your registration page may INCLUDE or REQUIRE Hydrogen/pgRegistration.php 
//		but should not BE this file, as doing that either in this code 
//		or in a hyperlink will put the user in the Hydrogen 
//    	subdirectory rather than the directory for your app 
// 		and then things will break due to relative references 
//		like the ones below
require_once ("Hydrogen/settingsLogin.php");
require_once ('Hydrogen/clsPasswdRules.php');
require_once ('Hydrogen/clsDataSource.php');
require_once ('Hydrogen/clsSQLBuilder.php');
require_once ('Hydrogen/libFilter.php');
require_once ('Hydrogen/libAuthenticate.php');

$username = sanitizePostVar('username');
$password = sanitizePostVar('password');
$last_name = sanitizePostVar('last_name');
$first_name = sanitizePostVar('first_name');
if (filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) $email = $_POST['email'];

//Default password rule is 12-character minumum. 
if (!isset($passwordRules)) {
	$passwordRules=new PasswordRules(false);
	$passwordRules->addRule("min",12,"/\S/","character");
}
$registration_message="";
$registration_success = "<h4>Registration successful.</h4>";
$rules = implode('<br>',$passwordRules->showRules());

if (isset($_POST['email'])) {
	if(isset($email)) {
		if ($passwordRules->checkPassword($_POST['password'])) {

			//validate username
			if (lookUpUsername($username) == '') {

				//register the user (in the database) with hashed password
				$sqlb = new SQLBuilder("INSERT");
				$sqlb->setTableName("user");
				$sqlb->addColumn("username",$username);
				$sqlb->addColumn("first_name",$first_name);
				$sqlb->addColumn("last_name",$last_name);
				$sqlb->addColumn("email",$email);
				$sqlb->addColumn("password",password_hash($password,PASSWORD_BCRYPT));
				$dds->setSQL($sqlb->getSQL());
				if (lookUpUsername($username) == $username) {
					// set session username
					$_SESSION['username'] = $username;
					//report success
					$registration_message = $registration_success;
					//
				} else $registration_message = "<h4>Unknown error with registration</h4>";
			} else {
				$registration_message ="<h4>Username not available</h4> ";
			}

		} else {
			$registration_message ="<h4>Password invalid</h4> " . $rules ;
		}
	} else {
		$registration_message ="<h4>E-mail address invalid</h4> ";
	}
} else {
		$registration_message ="<h4>". $rules . "</h4>";
	}

?>


      <h2>Registration form</h2>
      <p>Registration is quick and free! All fields below are required.</p>
	  <p>
	  <?php
	  echo ('<div class="pwdRules">' . $registration_message . '</div></p>');
  

	  
	  
	  //The form will POST to your custom registration page,
	  //Which will then (again) include this file to process the POST data

	  echo '<form action="' . $settings['registration_page'] . '" method="post" name="regForm" id="regForm" >';
	  ?>
	  
         <table>
          <tr>
            <td>Choose your username</td>
            <td><input name="username" type="text" id="user_name" minlength="4" maxlength="30" size="25" value="<?php if(isset($_POST['username'])) echo $username; ?>">
			
			<?php if($registration_message != $registration_success)
				echo '<input name="btnAvailable" type="button" id="btnAvailable" 		  value="Check Availability">';
			?>
			
              </td>
          </tr>
          <tr>
		    <td>first_name</td>
            <td><input name="first_name" type="text" id="fname"  maxlength="30" size="25" value="<?php if(isset($_POST['username'])) echo $first_name; ?>"></td>
		  </tr>
		  <tr>
		     <td>last_name</td>
		     <td><input name="last_name" type="text" id="lname"  maxlength="30" size="25" value="<?php if(isset($_POST['username'])) echo $last_name; ?>"></td>
		  </tr>
          <tr>
          <tr>
            <td>E-mail</td>
            <td><input name="email" type="text" id="usr_email"  maxlength="30" size="25" value="<?php if(isset($_POST['username'])) echo $email; ?>"></td>
          </tr>
		  
          <tr>
            <td>Password</td>
            <td><input name="password" type="password" id="pwd"  maxlength="30" size="25"></td>
          </tr>
          <tr>
            <td>Re-enter password</td>
            <td><input name="password2"  id="pwd2" type="password"  maxlength="30" size="25" equalto="#password"></td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
<?php /*		  
          <tr>
            <td width="22%"><strong>Image Verification </strong></td>
            <td width="78%"></td>
          </tr>
		  
*/ 
?>
        </table>
        <p align="center">
		<?php if($registration_message != $registration_success)
          echo '<input name="btnSubmit" type="submit" id="Register" value="Register">';
		?>
        </p>
      </form>

