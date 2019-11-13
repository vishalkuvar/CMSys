<?php 
/**
 * Save Attendance
 */
function sendResponse($response) {
	echo "<b id='wwww'>$response</b>";
}
// Get the Sem and branch.
if (isset($_POST["attended"])) {
	$attended = $_POST["attended"];
}
if (isset($_POST["aId"]) && !empty($_POST["aId"])) {
	$attendanceId = intval($_POST["aId"]);
}
if (isset($_POST["sId"]) && !empty($_POST["sId"])) {
	$studentId = intval($_POST["sId"]);
}
if (!isset($studentId) || !isset($attendanceId)) {
	sendResponse("-1");
} else {
	if ($attended == 'true')
		$attended = 1;
	else
		$attended = 0;
	$sql = "UPDATE `attendance_student` SET `attended`='$attended' WHERE `attendance_id`='$attendanceId' AND `student_id`='$studentId'";
	echo $sql;
	$login->DB->query($sql);
	if ($login->isError()) {
		sendResponse("0");
	}
	sendResponse("1");
}
?>