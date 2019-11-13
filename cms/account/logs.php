<?php
/**
 * View Logs Page
 */
// View Logs Template
echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/table.css">';
?>
<!-- Table -->
<table class="cmsys-table">
	<tr>
		<th>Sr.No</th>
		<th>Log</th>
		<th>Date</th>
		<th>IP</th>
	</tr>
<?php
	if (count($matches) == 3) {
		RedirectHandler::redirect('CMSYS_LOGS', 1);
	}
	// Get CurrentPage/Total Entries/Pages and Logs.
	$currentPage = intval($matches[3]);
	$page = max(0, $currentPage-1);
	$logs = $login->logs->getLogs($page);
	$totalEntries = intval($logs[1][0][0]);
	$totalPages = ceil($totalEntries/10);
	$i = 0;
	// If Log exists.
	if ($logs != NULL) {
		// Loop through each log and display them along with date.
		foreach($logs[0] as $log) {
			if ($i%2 == 0)
				echo "<tr>";
			else
				echo '<tr class="odd">';
			$i++;
			echo "<td>$i</td>";
			echo '<td>'. $log[0] .'</td>';
			echo '<td>'. date('D, Y-m-d h:i:s', $log[1]) .'</td>';
			echo '<td>'. $log[2] .'</td>';
		}
	} else {
		// Guess the correct Page and redirect.
		if ($page > 1) {
			if (!isset($step)) 
				$step = 10;
			$page = ceil($totalEntries/$step);	// Guess the Page
			RedirectHandler::redirect('CMSYS_LOGS', $page);
		}
	}
?>
</table>
<div style="margin: 5px; right: 30px; position: absolute">
<table cellpadding="2">
	<tr style="display: table-cell;">
	<?php
		// Page Handler/
		$start = max(1, min($totalPages, $currentPage-2));
		$end = min($totalPages, $currentPage+2);
		if ($currentPage == $totalPages) {
			$end = $currentPage;
		} else if ($currentPage == 1) {
			$start = $currentPage;
		}
		$j = 1;
		for ($i = $start; $i <= $end; $i++) {
			echo '<th><a href="'. RedirectHandler::getRedirectURL('CMSYS_LOGS', $i) .'">'. $i .'</th>';
		}
	?>
	</tr>
</table>
</div>
<?php
	echo "<br/>";
	echo '<br/>';
?>