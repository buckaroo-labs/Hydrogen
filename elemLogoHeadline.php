
<div class="w3-row w3-padding-64">
  
 <?php 
if (isset($logo_image)) {
echo '<div class="w3-third w3-container w3-hide-medium w3-hide-small"><p class="w3-center"><img src="' . $logo_image .'"></p></div>';
}
?>

 <?php 
if (isset($headline)) {
echo '<div class="w3-third w3-container w3-hide-medium w3-hide-small"><p class="w3-center">'. $headline .'</p></div>';
}
?>

</div>