<?php
//A sidebar for consistent look and feel sitewide
//$settings['facebook_page'] = "https://www.facebook.com/pages/MyPageName/1234567890";
if (!isset($settings['color4']))  $settings['color4']="w3-hover-blue";

if (!isset($sidebar_links)) {
	$sidebar_links=[];
	//These are the (demo) defaults. Override them in an included settings file and/or page template.
	$sidebar_links[sizeof($sidebar_links)]=array("name"=>"Link","href"=>"#","class"=> ' '.  
		$settings['color4'] );
	$sidebar_links[sizeof($sidebar_links)]=array("name"=>"Link","href"=>"#","class"=>' ' .  
		$settings['color4'] );
	$sidebar_links[sizeof($sidebar_links)]=array("name"=>"Link","href"=>"#","class"=>' ' .  
		$settings['color4'] );
	$sidebar_links[sizeof($sidebar_links)]=array("name"=>"Link","href"=>"#","class"=>' ' .  
		$settings['color4'] );
}
?>

<!-- Sidebar -->
<nav class="w3-sidebar w3-bar-block w3-collapse w3-large w3-theme-l5 w3-animate-left" id="HSidebar">
  <a href="javascript:void(0)" onclick="w3_close()" class="w3-right w3-xlarge w3-padding-large 
    <?php echo 	$settings['color4'] ; ?>
  w3-hide-large" title="Close Menu">
    <i class="fa fa-remove"></i>
  </a>
 <h4 class="w3-bar-item"><b>Menu</b></h4>
 <table>
<?php  

$arrlength=count($sidebar_links);
for($x=0;$x<$arrlength;$x++)   {
  echo '<tr><td><a href="' . $sidebar_links[$x]['href'];
  if (isset($_GET['menu'])) echo "?menu=" . $stateVar['menu'];
  echo '" class="w3-bar-item w3-button ' . $sidebar_links[$x]['class'] . '">' . $sidebar_links[$x]['name'] .
  	 '</a></td></td></tr>';
  echo "";
}


?>
</table>

<?php 
if (isset($settings['facebook_page'])) {
echo ('<a target="_blank" class="w3-bar-item w3-button '. $settings['color4'] . '" href="'
 . $settings['facebook_page'] . '"><img class="HSidebarSiteImg" src="Hydrogen/images/sites/Facebook.png" alt="Facebook"></a>');
}
if (isset($settings['instagram_page'])) {
echo ('<a target="_blank" class="w3-bar-item w3-button '. $settings['color4'] . '" href="'
 . $settings['instagram_page'] . '"><img class="HSidebarSiteImg" src="Hydrogen/images/sites/Instagram.png" alt="Instagram"></a>');
}
if (isset($settings['github_page'])) {
echo ('<a target="_blank" class="w3-bar-item w3-button '. $settings['color4'] . '" href="'
 . $settings['github_page'] . '"><img class="HSidebarSiteImg" src="Hydrogen/images/sites/GitHub.png" alt="GitHub"></a>');
}
if (isset($settings['stackoverflow_page'])) {
echo ('<a target="_blank" class="w3-bar-item w3-button '. $settings['color4'] . '" href="'
 . $settings['stackoverflow_page'] . '"><img class="HSidebarSiteImg" src="Hydrogen/images/sites/StackOverflow.png" alt="StackOverflow"></a>');
}
if (isset($settings['linkedin_page'])) {
echo ('<a target="_blank" class="w3-bar-item w3-button '. $settings['color4'] . '" href="'
 . $settings['linkedin_page'] . '"><img class="HSidebarSiteImg" src="Hydrogen/images/sites/LinkedIn.png" alt="LinkedIn"></a>');
}
?>

</nav>

<!-- Overlay effect when opening sidebar on small screens 
<div class="w3-overlay w3-hide-large" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay">

</div>
-->

<script>
// Get the Sidebar
var HSidebar = document.getElementById("HSidebar");

// Get the DIV with overlay effect
//var overlayBg = document.getElementById("myOverlay");

// Toggle between showing and hiding the sidebar, and add overlay effect
function w3_open() {
  if (HSidebar.style.display === 'block') {
    HSidebar.style.display = 'none';
    //overlayBg.style.display = "none";
  } else {
    HSidebar.style.display = 'block';
    //overlayBg.style.display = "block";
  }
}


// Close the sidebar with the close button
function w3_close() {
  HSidebar.style.display = "none";
  //overlayBg.style.display = "none";
}
</script>