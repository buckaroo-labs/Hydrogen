
<div class="w3-row w3-padding-24">
  
 <?php 
if (isset($headline)) {
    if (isset($logo_image)) {
        echo '<div class="w3-quarter w3-container w3-hide-medium w3-hide-small"><p class="w3-center"><img src="' . $logo_image .'"></p></div>';
        }
echo '<div class="w3-twothird w3-container w3-hide-medium w3-hide-small" style="color:#2222DD;"><p class="w3-center">'. $headline .'</p></div>';
}
?>

</div>
