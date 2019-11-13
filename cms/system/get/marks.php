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

// IF Branch/Sem is not set, send no response.
if (!isset($sem)) {
	sendResponse("");
} else {
	// Get UserID
	// SQL Query to fetch neccessary data about Marks.
	$sql = "SELECT `login`.`name` AS `checked_by`,`ps2`.`id`, `pm`.`name`, `pm`.`date`, `pm`.`marks`,
				`ps2`.`tmarks`, `pm`.`subject_code`,
				(SELECT  COUNT(*)
					FROM `paper_student` AS `ps1`
        			WHERE `ps1`.`tmarks` >= `ps2`.`tmarks` AND `ps1`.`paper_id`=`pm`.`id`
        		) AS `rank`
				FROM `paper_main` `pm`
				RIGHT JOIN `paper_student` AS `ps2` ON
				`pm`.`id`=`ps2`.`paper_id` AND `ps2`.`student_id`='". $login->id ."'
				AND `ps2`.`checked`='2'
				RIGHT JOIN `login` ON 
				`ps2`.`checked_by`=`login`.`id`
				WHERE `pm`.`semester`='$sem'";
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