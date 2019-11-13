<?php
	if (count($matches) != 5) {
		$login->addError("Invalid Link");
		$login->errorRedirect("CMSYS_PROFILE");
	}
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/table.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/switch.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/input2.css">';
	echo '<script type="text/javascript" src="'.$rootDir.'/js/ajax.js"></script>';
	$attendanceId = intval($matches[4]);
	// Get all Students of that semester and branch
	$sql = "SELECT `login`.`name`, `login`.`id`,`attendance_student`.`attended` FROM `login` RIGHT JOIN `attendance_student` ON `login`.`id`=`attendance_student`.`student_id` WHERE `login`.`title` = '1' AND `attendance_student`.`student_id` = `login`.`id` AND `attendance_student`.`attendance_id`='$attendanceId'";
	$login->DB->query($sql);
	if ($login->DB->result->num_rows == 0) {
		$login->addError("Cannot Retrieve List of Students");
		$login->errorRedirect("CMSYS_PROFILE");
	}
	// Some Hidden input(Used in JS)
	echo "<input hidden id='attendanceId' value='$attendanceId'>";
	// Common Template
	include dirname(__FILE__) .'/add_table_template.inc.php';
?>