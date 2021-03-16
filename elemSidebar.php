<?php
//A sidebar for consistent look and feel sitewide
//$facebook_page = "https://www.facebook.com/pages/MyPageName/1234567890";

if (!isset($sidebar_links)) {
	//These are the (demo) defaults. Override them in an included settings file and/or page template.

	$sidebar_links[0]=array("name"=>"Link","href"=>"#","class"=>"w3-hide-small w3-hover-black");
	$sidebar_links[1]=array("name"=>"Link","href"=>"#","class"=>"w3-hide-small w3-hover-black");
	$sidebar_links[2]=array("name"=>"Link","href"=>"#","class"=>"w3-hide-small w3-hover-black");
	$sidebar_links[3]=array("name"=>"Link","href"=>"#","class"=>"w3-hide-small w3-hover-black");
}
?>

<!-- Sidebar -->
<nav class="w3-sidebar w3-bar-block w3-collapse w3-large w3-theme-l5 w3-animate-left" id="mySidebar">
  <a href="javascript:void(0)" onclick="w3_close()" class="w3-right w3-xlarge w3-padding-large w3-hover-black w3-hide-large" title="Close Menu">
    <i class="fa fa-remove"></i>
  </a>
 <h4 class="w3-bar-item"><b>Menu</b></h4>
 <table>
<?php  

$arrlength=count($sidebar_links);
for($x=0;$x<$arrlength;$x++)   {
  echo '<tr><td><a href="' . $sidebar_links[$x]['href'];
  if (isset($_GET['menu'])) echo "?menu=" . $stateVar['menu'];
  echo '" class="w3-bar-item w3-button ' . $sidebar_links[$x]['class'] . '">' . $sidebar_links[$x]['name'] . '</a></td></td></tr>';
  echo "";
}


if (isset($facebook_page)) {
echo ('<a target="_blank" class="w3-bar-item w3-button w3-hover-black" href="' . $facebook_page . '"><img src="/images/facebook.jpg" alt="Facebook" height="90" width="90"></a>');
}
?>
</table>
</nav>

<!-- Overlay effect when opening sidebar on small screens -->
<div class="w3-overlay w3-hide-large" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>

<script>
// Get the Sidebar
var mySidebar = document.getElementById("mySidebar");

// Get the DIV with overlay effect
var overlayBg = document.getElementById("myOverlay");

// Toggle between showing and hiding the sidebar, and add overlay effect
function w3_open() {
  if (mySidebar.style.display === 'block') {
    mySidebar.style.display = 'none';
    overlayBg.style.display = "none";
  } else {
    mySidebar.style.display = 'block';
    overlayBg.style.display = "block";
  }
}


// Close the sidebar with the close button
function w3_close() {
  mySidebar.style.display = "none";
  overlayBg.style.display = "none";
}
</script>