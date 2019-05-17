<?php
//A footer for consistent look and feel sitewide
if (!isset($footer_text)) $footer_text="<h4>Default footer text</h4>";
?>
<!-- shift it to the right by 250 pixels when the sidebar is visible -->
<div class="w3-main" style="margin-left:250px">
  <footer id="myFooter">
    <div class="w3-container w3-theme-l2 w3-padding-32">
      <?php echo $footer_text; ?>

    </div>

    <div class="w3-container w3-theme-l1">
      <p><a href="https://github.com/ke7ijo/Hydrogen" target="_blank">Hydrogen</a> powered</p>
    </div>
  </footer>
</div>



