<?php
//A footer for consistent look and feel sitewide

if (!isset($settings['color1']))  $settings['color1']="w3-green";
if (!isset($settings['color2']))  $settings['color2']="w3-blue";

if(isset($_GET['layout']) && $_GET['layout']=='iframe') {
    $layout='iframe';
}

if ($layout!='iframe') {
	echo '<div class="w3-main w3-test2 ' . $_GET['layout'] .'" style="margin-left:250px">';
} else {
	echo '<div class="w3-main ' . $layout .'" style="display:none;">';
}
?>
<!-- shift it to the right by 250 pixels when the sidebar is visible -->

  <footer id="myFooter" style="margin-top: 30px; max-width: 100%">
    <div class="w3-container <?php echo $settings['color1'] ?> w3-padding-32">
      <?php if (isset($settings['footer_text1'])) echo $settings['footer_text1']; ?>

    </div>

    <div class="w3-container <?php echo $settings['color2'] ?>">
       <?php if (isset($settings['footer_text2'])) echo $settings['footer_text2']; ?>
    </div>
  </footer>
</div>



