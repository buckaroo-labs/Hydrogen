<?php

function paginate($dataSource,$page_num=1) {
	global $page_count;
	debug ("pagination.php: function: paginate");
	if ($page_num < 1) $page_num=1;
	$result = $dataSource->setPageNum($page_num);

}

//a newVars($pg) function is required in every page that calls this function
//to keep track of whatever GET variables have been processed including the page number

function showPagination($dataSource,$scriptName) {
	global $page_num;
	if (!isset($page_num)) $page_num=1;
	debug ("pagination.php: function: showPagination");
	$page_count = $dataSource->getPageCount();
	echo '<ul class="pagination">';

	if ($page_count >1) {

		if ($page_num > 1) {
			//show 'prev'
			$prev_link=$scriptName . newVars($page_num - 1);
			echo "<li><a href='$prev_link' class='prevlink'>Previous </a></li>";
		}

		//show page position
		echo "<li> Page $page_num of $page_count </li>";

		if ($page_num < $page_count) {
			//show 'next'
			$next_link=$scriptName . newVars($page_num + 1);
			echo "<li><a href='$next_link' class='nextlink'> Next</a></li>";
		}
	}
	echo '</ul>';
}


?>