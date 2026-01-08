<?php 
if (!isset($settings['color1']))  $settings['color1']="w3-green";
if (!isset($settings['color2']))  $settings['color2']="w3-blue";
if (!isset($settings['color3']))  $settings['color2']="w3-hover-white";

if (!isset($navbar_links)) {
	//These are the (demo) defaults. Override them in an included settings file and/or page template.

	$navbar_links[0]=array("name"=>'Logo',"href"=>"#","class"=>"w3-theme-l1");
	//Put your logo image here below in place of "Logo" text above
	//$navbar_links[0]=array("name"=>'<img src="images/logo_thumbnail.jpg">',"href"=>"#","class"=>"w3-theme-l1");
	$navbar_links[sizeof($navbar_links)]=array("name"=>"Home","href"=>"./","class"=> $settings['color3']);
	//Best to hide most navbar links on smaller screens, or else they overlap the sidebar
	$navbar_links[sizeof($navbar_links)]=array("name"=>"About","href"=>"#","class"=>"w3-hide-small " . $settings['color3']);
	$navbar_links[sizeof($navbar_links)]=array("name"=>"News","href"=>"#","class"=>"w3-hide-small " . $settings['color3']);
	$navbar_links[sizeof($navbar_links)]=array("name"=>"Contact","href"=>"#","class"=>"w3-hide-small " . $settings['color3'] );
}
if(isset($_GET['layout']) && $_GET['layout']=='iframe') {
    $layout='iframe';
}
if ($layout!='iframe') {
	echo '<div class="w3-top">';
} else {
	echo '<div class="w3-top" style="display:none;">';
}
?>
<!-- Navbar -->
  <div class="w3-bar  <?php echo $settings['color1'] ?> w3-top w3-left-align w3-large">
    <a class="w3-bar-item w3-button w3-right w3-hide-large <?php echo $settings['color1'] ?>" w3-large  <?php echo $settings['color3'] ?>" href="javascript:void(0)" onclick="w3_open()"><i class="fa fa-bars"></i></a>
<?php 

	$arrlength=count($navbar_links);
	for($x=0;$x<$arrlength;$x++)   {
	  echo '<a href="' . $navbar_links[$x]['href'] . '" class="w3-bar-item w3-button ' . $navbar_links[$x]['class'] . '">' . $navbar_links[$x]['name'] . '</a>';
	  echo "";
	}
	//default behavior is to show the search form and login status.
		
	if (!isset($hideSearchForm)) include('Hydrogen/elements/SearchForm.php');  
	if (!isset($hideLoginStatus)) include('Hydrogen/elements/LoginStatusbar.php'); 
?>

  </div>
</div>
