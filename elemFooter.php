<?php
//A footer for consistent look and feel sitewide

if (!isset($settings['color1']))  $settings['color1']="w3-green";
if (!isset($settings['color2']))  $settings['color2']="w3-blue";
?>
<!-- shift it to the right by 250 pixels when the sidebar is visible -->
<div class="w3-main" style="margin-left:250px">
  <footer id="myFooter">
    <div class="w3-container <?php echo $settings['color1'] ?> w3-padding-32">
      <?php if (isset($settings['footer_text1'])) echo $settings['footer_text1']; ?>

    </div>

    <div class="w3-container <?php echo $settings['color2'] ?>">
       <?php if (isset($settings['footer_text2'])) echo $settings['footer_text2']; ?>
    </div>
  </footer>
</div>



