<?php
//defaults:
$settings['DEFAULT_DB_TYPE'] = "mysql";
$settings['DEFAULT_DB_USER'] = "username";
$settings['DEFAULT_DB_PASS'] = "password";
$settings['DEFAULT_DB_HOST'] = "localhost";
$settings['DEFAULT_DB_PORT'] = "1521";
$settings['DEFAULT_DB_INST'] = "XE";
$settings['DEFAULT_DB_MAXRECS'] = 150;
//put actual passwords here:
include ("settingsPasswords.php");

$settings['color1']="w3-blue";
$settings['color2']="w3-green";
$settings['color3']="w3-hover-white";

$logo_image="../images/logo.png";
$settings['login_page']="login.php";
$hideSearchForm=true;
$settings['footer_text1']='&copy;2025 My Site Inc Ltd';

//2-week expiry
$settings['JWTExpireTime'] = 1209600;
$settings['JWTTokenName'] = 'persistentLogin';

$navbar_links=array();  
$sidebar_links=array();  
if (!isset($_GET['menu'])) {
	$sidebar_links[sizeof($sidebar_links)]=array("name"=>'Page 1',"href"=>"1.php","class"=>"w3-hover-blue");
    $sidebar_links[sizeof($sidebar_links)]=array("name"=>'Page 2',"href"=>"2.php","class"=>"w3-hover-blue");
    $sidebar_links[sizeof($sidebar_links)]=array("name"=>'Page 3',"href"=>"3.php","class"=>"w3-hover-blue");
    $sidebar_links[sizeof($sidebar_links)]=array("name"=>'Page 4',"href"=>"4.php","class"=>"w3-hover-blue");
}
$active_menu_class="w3-hide-small w3-black";
$other_menu_class="w3-hide-small w3-hover-black";
$navbar_links[0]=array("name"=>'<img src="../images/icon2.png" height="20">',"href"=>"index.php","class"=>"w3-theme-l2");
$navbar_links[1]=array("name"=>"Home","href"=>"index.php","class"=>"w3-hide-small w3-hover-white");
$navbar_links[2]=array("name"=>"Dashboard","href"=>"dashboard.php","class"=>"w3-hover-white");


?>
