
<div class="w3-row w3-padding-64">
  
 <?php 
if (isset($logo_image)) {
echo '<div class="w3-third w3-container"><p class="w3-padding-large w3-padding-32 w3-center"><img src="' . $logo_image .'"></p></div>';
}
?>

 <?php 
if (isset($headline)) {
echo '<div class="w3-third w3-container"><p class="w3-padding-large w3-padding-32 w3-center">'. $headline .'</div>';
}
?>

</div>