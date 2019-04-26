<?php 

//$logo_image = "/images/logo.jpg";
//$headline = "My Page"

?>
<table>
<tbody>
<tr>
<td>
<?php 
if (isset($logo_image)) {

echo '<div id="logo"><img style="width: 120px; height: 120px;" alt="mem" src="' . $logo_image .'"></div>';
}
?>

</td>
<td>

<?php 
if (isset($headline)) {
echo $headline; 
}
?>

</td>
</tr>
</tbody>
</table>
