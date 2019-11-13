<?php
// Check for URL
if (count($matches) != 5) {
	// Invalid URl, check if titleType is present.
	RedirectHandler::redirect('CMSYS_LIST_ATTENDANCE', 1);
}
// Save Type
$redirectURL = 'CMSYS_LIST_ATTENDANCE';
// Save Member ID.
$attendanceId = intval($matches[4]);
// Remove Paper from main and student.
$sql = "DELETE FROM `attendance_main` WHERE `id`='$attendanceId'";
$login->DB->query($sql);
$sql = "DELETE FROM `attendance_student` WHERE `attendance_id`='$attendanceId'";
$login->DB->query($sql);
$login->addInfo('Attendance Deleted');
// Log
$login->logs->insertLogByType(CMSYS_LOG_DELETE_ATTENDANCE, array('reasona' => 'Id: $attendanceId'));
$login->redirect($redirectURL);
?>