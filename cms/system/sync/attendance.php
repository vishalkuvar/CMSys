<?php
/**
 * Get Subjects and Marks
 */
// Sends the Response.
function sendResponse($response) {
	echo "<b id='wwww'>$response</b>";
}
// Get the Sem and branch.
if (isset($_POST["sem"]) && !empty($_POST["sem"])) {
	$sem = $_POST["sem"];
}
if (isset($_POST["user"]) && !empty($_POST["user"])) {
	$user = $_POST["user"];
}
// IF Branch/Sem is not set, send no response.
if (!isset($user) || !isset($sem)) {
	sendResponse("");
} else {
	// SQL Query to fetch neccessary data about Attendnace.
	$sql = "SELECT `login`.`name` AS `added_by`, `am`.`topics`, `am`.`attendance` as `total_lecture`,
					`am`.`subject_code`, `am`.`date`, `as`.`attended`
					FROM `attendance_main` `am`
					RIGHT JOIN `attendance_student` AS `as` ON
					`am`.`id`=`as`.`attendance_id` AND `as`.`student_id`='$user'
					RIGHT JOIN `login` ON
					`am`.`teacher_id`=`login`.`id`
					WHERE `am`.`semester`='$sem'";
	$login->DB->query($sql);
	// Generate Array and save result into it.
	$a = array();

	if ($login->DB->result->num_rows > 0) {
		while (($res = $login->DB->result->fetch_assoc())) {
			$res['subject'] = $login->allSubjects[array_search($res['subject_code'], $login->allSubjects) + 1];
			$a[] = $res;
		}
		// Json Encode, so js can read it.
		sendResponse(json_encode($a));
	} else {
		sendResponse(json_encode(""));
	}
}

?>