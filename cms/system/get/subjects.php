<?php
/**
 * Get Subjects for listing
 */
// Sends the Response.
function sendResponse($response) {
	echo "<b id='wwww'>$response</b>";
}
// Get the Sem and branch.
if (isset($_POST["sem"]) && !empty($_POST["sem"])) {
	$sem = $_POST["sem"];
}
if (isset($_POST["branch"]) && !empty($_POST["branch"])) {
	$branch = intval($_POST["branch"]);
}
$type = 0;
if (isset($_POST["type"]) && !empty($_POST["type"])) {
	$type = intval($_POST["type"]);
}
if (isset($_POST["subjects"]) && !empty($_POST["subjects"])) {
	$subjectCodes = $_POST["subjects"];
}
// Autoselect is OFF by default.
$autoselect = false;
if (isset($_POST["autoselect"])) {
	if (isset($_POST["id"])) {	// ID should exist if autoselect is true.
		$id = intval($_POST["id"]);
		$autoselect = true;
	}
}

// IF Branch/Sem is not set, send no response.
if (!isset($branch) || !isset($sem)) {
	sendResponse("");
} else {	// Type=2 => Edit
	if ($type == 2) {
		$semA = $sem;
	} else {
		$semA = array($sem);
	}
	$options = '';
	$subjects = array();
	// If AutoSelect is set, get the subjects teacher teaches and save into array.
	if ($autoselect) {
		$login->DB->query("SELECT `subject_code` FROM `teacher_subjects` WHERE `teacher_id`='$id'");
		if ($login->DB->result->num_rows > 0) {
			while (($res = $login->DB->result->fetch_assoc())) {
				$subjects[] = $res['subject_code'];
			}
		}
	}
	for ($i = 0; $i < count($semA); $i++) {
		// Loop through each subject of the branch.
		$options .= '<option disabled value="0">'. $login->allYears[$semA[$i]][2] .'</option>';
		foreach ($login->subjectBranch[$branch][$semA[$i]] as $subject) {
			if (isset($subjectCodes) && !in_array($subject[0], $subjectCodes))
				continue;
			$s = '';
			// If Teacher teaches it, select it(autoselect feature.)
			if (in_array($subject[0], $subjects))
				$s = 'selected';
			// Generates option Tag.
			$options .= '<option '. $s .' value="'. $subject[0] .'">'. $subject[1] .'</option>';
		}
	}
	// Sends back option tag
	sendResponse($options);
}

?>