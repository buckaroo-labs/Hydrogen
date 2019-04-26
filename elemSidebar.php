<?php
//A sidebar for consistent look and feel sitewide
//$facebook_page = "https://www.facebook.com/pages/MyPageName/1234567890";
?>

<div id="sidebar">

<div class="sidebar_item" id="sidebar_item0">
<div class="desc"><a href="/page1.php">Home</a>
</div>
</div>
<div class="sidebar_item" id="sidebar_item1">
<div class="desc"><a href="/page1.php">Page One</a>
</div>
</div>

<div class="sidebar_item" id="sidebar_item2">
<div class="desc"><a href="/page2.php">Page Two</a>
</div>
</div>

<div class="sidebar_item" id="sidebar_item3">
<div class="desc"><a href="/page3.php">Page Three</a>
</div>
</div>

<?php
if (isset($facebook_page)) {
echo ('<div class="sidebar_item" id="Facebook">');
echo ('<a target="_blank" href="' . $facebook_page . '">');
echo ('<img src="/images/facebook.jpg" alt="Facebook" height="90" width="90"></a>');
echo ('<div class="desc">Follow us on <br>');
echo ('<a href="' . $facebook_page . '">Facebook');
echo ('</a></div>');
echo ('</div>');
}
?>

</div>

