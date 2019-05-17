<?php

/* Label for username prompt ("Username","Username or email","email address", etc.) */
//Label should reflect the logic implemented in the authentication file
if (!isset($settings['uname_label'])) $settings['uname_label']='Username';

/* Show link to registration page on login page 1=yes; 0=no */
if (!isset($settings['prompt_reg'])) $settings['prompt_reg']='1';

/* Include link to account maint page on login page or status bar, 1=yes; 0=no */
if (!isset($settings['account_maint'])) $settings['account_maint']='1';

if (!isset($settings['login_page'])) $settings['login_page']="login.php";
if (!isset($settings['registration_page'])) $settings['registration_page']="register.php";

?>


