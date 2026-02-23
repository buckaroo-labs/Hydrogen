<div class="w3-row w3-padding-24">

<?php 
	if (array_key_exists('layout',$_GET) && $_GET['layout']=='iframe') {
		//do nothing
	}
	elseif (isset($headline)) {
		if (isset($logo_image)) {
			echo '<div id="HLogo" class="w3-quarter w3-container w3-hide-medium w3-hide-small">
					<p class="w3-center"><img src="' . $logo_image .'"></p>
				</div>';
			}
		echo '<div id="HHeadline" class="w3-twothird w3-container w3-hide-medium w3-hide-small">
			<p class="w3-center">'. $headline .'</p>
		</div>';
	}
?>

</div>
