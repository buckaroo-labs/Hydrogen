<?php 
if (!isset($navbar_links)) {
	//These are the (demo) defaults. Override them in an included settings file and/or page template.

	$navbar_links[0]=array("name"=>'Logo',"href"=>"#","class"=>"w3-theme-l1");
	//Put your log image here below in place of "Logo" text above
	//$navbar_links[0]=array("name"=>'<img src="images/logo_thumbnail.jpg">',"href"=>"#","class"=>"w3-theme-l1");
	$navbar_links[1]=array("name"=>"Home","href"=>"./","class"=>"w3-hide-small w3-hover-white");
	$navbar_links[2]=array("name"=>"About","href"=>"#","class"=>"w3-hide-small w3-hover-white");
	$navbar_links[3]=array("name"=>"News","href"=>"#","class"=>"w3-hide-small w3-hover-white");
	$navbar_links[4]=array("name"=>"Contact","href"=>"#","class"=>"w3-hide-small w3-hover-white");
}
?>

<!-- Navbar -->
<div class="w3-top">
  <div class="w3-bar w3-green w3-top w3-left-align w3-large">
    <a class="w3-bar-item w3-button w3-right w3-hide-large w3-hover-white w3-large w3-green" href="javascript:void(0)" onclick="w3_open()"><i class="fa fa-bars"></i></a>
<?php 

	$arrlength=count($navbar_links);
	for($x=0;$x<$arrlength;$x++)   {
	  echo '<a href="' . $navbar_links[$x]['href'] . '" class="w3-bar-item w3-button ' . $navbar_links[$x]['class'] . '">' . $navbar_links[$x]['name'] . '</a>';
	  echo "";
	}
	//default behavior is to show the search form and login status.
		
	if (!isset($hideSearchForm)) include('Hydrogen/elemSearchForm.php');  
	if (!isset($hideLoginStatus)) include('Hydrogen/elemLoginStatusbar.php'); 
?>

  </div>
</div>