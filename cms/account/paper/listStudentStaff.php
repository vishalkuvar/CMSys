<?php
	// Load CSS and JS
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/table.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/input2.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/dropdown.css">';
	echo '<script type="text/javascript" src="'.$rootDir.'/js/dropdown.js"></script>';

	// Show Error
	$count = ErrorHandler::showError();

	// If No error, then proceed with showing tables.
	if (!$count) {
		// There should be 6th deep URL.
		if (count($matches) != 5) {
			$login->addError("Invalid URL Entered.");
			RedirectHandler::redirect('CMSYS_LIST_PAPER');
		}
		// Check the type
		$paperId = intval($matches[4]);
		$redirectURL = 'CMSYS_LIST_PAPER';
		// Get all Students of that semester and branch
		$sql = "SELECT `login`.`name`, `login`.`id`,`paper_student`.`uploaded`,`paper_student`.`checked`,`paper_student`.`tMarks` FROM `login` RIGHT JOIN `paper_student` ON `login`.`id`=`paper_student`.`student_id` AND `paper_student`.`paper_id`='$paperId' WHERE `login`.`title` = '1'";
		$login->DB->query($sql);
		// Some Hidden input
		echo "<input hidden id='paperId' value='$paperId'>";
		include 'include_listStaff.inc.php';
	}
?>