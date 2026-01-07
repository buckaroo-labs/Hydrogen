<?php
//defaults:
$settings['DEFAULT_DB_TYPE'] = "sqlite";
//The following settings would be needed for an Oracle or MySQL connection:
$settings['DEFAULT_DB_USER'] = "username";
$settings['DEFAULT_DB_HOST'] = "localhost";
$settings['DEFAULT_DB_PORT'] = "1521";
$settings['DEFAULT_DB_INST'] = "XE";
$settings['DEFAULT_DB_MAXRECS'] = 150;
//Because this file may not be ignored by git, don't put a password 
//  in this file, but use this format:
$settings['DEFAULT_DB_PASS'] = "password"; 
//put any required passwords in this file instead:
@include ("settingsPasswords.php");

//If these color settings are not set here, defaults may be assigned elsewhere in the code.
//These aren't colors per se, but classes assigned to elements, which will be colored
//according to w3.css specs. You can use the w3 color classes or something else defined in your 
//styles.css file. ... or even override the colors of the w3 classes in your css. 
$settings['color1']="w3-blue";
$settings['color2']="w3-green";
//button hover colors
$settings['color3']="w3-hover-white";
$settings['color4']="w3-hover-blue";
$settings['color5']="w3-hover-black";

// A/B Testing:
$abTest=false;
if ($abTest) {
	$settings['color1']="w3-red";
	$settings['color2']="w3-black";

	$settings['color3']="w3-hover-black";
	$settings['color4']="w3-hover-red";
	$settings['color5']="w3-hover-white";
}


$logo_image="logo.png";
$settings['login_page']="login.php";
$hideSearchForm=true;
$settings['footer_text1']='&copy;2025 My Site Inc Ltd';

//2-week expiry
$settings['JWTExpireTime'] = 1209600;
$settings['JWTTokenName'] = 'persistentLogin';

$navbar_links=array();  
$sidebar_links=array();  
if (!isset($_GET['menu'])) {
	$sidebar_links[sizeof($sidebar_links)]=array("name"=>'Page 1',"href"=>"1.php","class"=>$settings['color4']);
    $sidebar_links[sizeof($sidebar_links)]=array("name"=>'Page 2',"href"=>"2.php","class"=>$settings['color4']);
    $sidebar_links[sizeof($sidebar_links)]=array("name"=>'Page 3',"href"=>"3.php","class"=>$settings['color4']);
    $sidebar_links[sizeof($sidebar_links)]=array("name"=>'Page 4',"href"=>"4.php","class"=>$settings['color4']);
}

//For testing:
//unset ($sidebar_links);

$active_menu_class="w3-hide-small " . $settings['color5'];
$other_menu_class="w3-hide-small " . $settings['color5'];
$navbar_links[sizeof($navbar_links)]=array("name"=>'<img src="logo.png" height="20">',"href"=>"index.php","class"=>"w3-theme-l2");
$navbar_links[sizeof($navbar_links)]=array("name"=>"Home","href"=>"index.php","class"=> $settings['color3']);
//Best to hide most navbar links on smaller screens, or else they overlap the sidebar
$navbar_links[sizeof($navbar_links)]=array("name"=>"Navbar item A","href"=>"a.php","class"=>"w3-hide-small " .$settings['color3']);
$navbar_links[sizeof($navbar_links)]=array("name"=>"Navbar item B","href"=>"b.php","class"=>"w3-hide-small " .$settings['color3']);


?>
