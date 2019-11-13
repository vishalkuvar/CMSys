<?php
$log = '';
/**
 * Checks for Post Argument,
 * inserts data into global $data and $column if POST is not empty.
 * @method emptyCheck
 * @param  string     $var         POST Argument
 * @param  int     $addEmpty    value to be added in $empty
 * @param  string     $forceValue  (default: NULL), if provided, uses this instead of $_POST
 * @param  bool       $delayInsert (default:false), to insert $data of current var or to delay it.
 * @return string                  Contents of POST Argument.
 */
function emptyCheck($var, $addEmpty, $forceValue = NULL, $delayInsert = false) {
	global $empty, $data, $column, $log;
	$temp = NULL;
	if ($forceValue != NULL)
		$_POST[$var] = $forceValue;
	if (!empty($_POST[$var])) {
		$temp = $_POST[$var];
		if (!$delayInsert) {
			if ($var != "prev_sem") {
				$data[] = $temp;
				$column[] = $var;
			}
			$log .= "$var=$temp, ";
		}
		$empty |= $addEmpty;
	}
	return $temp;
}
// Check for Member ID
if (count($matches) != 6) {
	if (count($matches) == 5) {
		RedirectHandler::getRedirectType($matches[4]);
		RedirectHandler::redirect('CMSYS_LIST_'. $semiURL, 1);
	} else {
		$login->addError("Invalid UserID.");
		RedirectHandler::redirect('CMSYS_PROFILE');
	}
}
$type = $matches[4];
// Get RedirectType
RedirectHandler::getRedirectType($type);
$redirectURL = 'CMSYS_LIST_'. $semiURL;
// Save Member ID.
$userId = intval($matches[5]);
$data = array();
$column = array();
$empty = 0;

// Seperately chekc if Member ID  is provided in Post.
if (empty($_POST['id'])) {
	$login->addError("UserID not Provided").
	$login->redirect($redirectURL, 1);
}
// Gather all Useful Variables.
$user = emptyCheck('user', 1);
$name = emptyCheck('name', 2);
$email = emptyCheck('email', 4);
$title = emptyCheck('title', 8, NULL, true);
$branch = emptyCheck('branch', 16);
if (isset($_POST["semester"]) && !empty($_POST["semester"])) {
	$semester = $_POST["semester"];
	$empty += 32;
	$column[] = "semester";
	$data[] = $semester[count($semester)-1];	// Last Semester is the main semester
	$log .= "Semester:". serialize($semester);
}
if (isset($_POST["subjects"]) && !empty($_POST["subjects"])) {
	$subjects = $_POST["subjects"];
	$empty += 64;
	$log .= "Subjects:". serialize($subjects);
}
$prevSem = emptyCheck('prev_sem', 128);
if ($empty == 0) {
	ErrorHandler::addError("No Details Provided to Update.");
}

// Check if User Already Exists
if (($empty&1) > 0 && $login->getColumnById('user', $userId) != $user && $login->userExists($user)) {
	$login->addError("Please choose another username.");
} else if (($empty&4) > 0 && !$login->isValidEmail($email)) {	// Email should be in proper format.
	$login->addError("Please Enter Valid Email(". ($empty&4) .").");
} else if (($empty&8) > 0 && !($title = RoleHandler::getTitleId($title))) {	// Valid Title should be given.
	$login->addError("Please Enter Valid Title");
} else if (($empty&16) > 0 && !$login->isValidBranch($branch)) {	// Branch should be valid.
	$login->addError("Please Enter Valid Branch");
} else if (($empty&32) > 0 && !$login->isValidSem($semester[count($semester)-1])) {	// Semester should be valid.
	$login->addError("Please Enter Valid Semester");
} else if ($login->getColumnById('title', $userId) != $title) {		// The User should exist and should be a student.
	$login->addError("Cannot Change Title of User");
} else if (($empty&128) > 0 && $prevSem != "on") {
	$login->addError("Invalid Previous Sem Data");
}
// Insert TitleID
emptyCheck('title', 8, $title);
$login->errorRedirect($redirectURL, 1);
// If Prev Sem is to be saved, and current sem is changed.
if (($empty&128) > 0 && ($empty&32) > 0) {
	$semOld = $login->getColumnById('semester', $userId);
	if ($login->isValidSem($semOld)) {	// Check if Old Sem was valid. (Sem0 is invalid.)
		$sql = "REPLACE INTO `student_semesters` (`user_id`, `semester`) VALUES ('$userId', '$semOld')";
		$login->DB->query($sql);
	}
}
// Update List of Semesters
if ($login->getColumnById('title', $userId) > 1 && ($empty&32) > 0) {
	$sql = "DELETE FROM `teacher_semesters` WHERE `teacher_id`='$userId'";
	$login->DB->query($sql);
	$sql = "";
	for ($i = 0; $i < count($semester); $i++) {
		$sql .= "REPLACE INTO `teacher_semesters` (`teacher_id`, `semester`) VALUES ('$userId', '". $semester[$i] ."');";
	}
	$login->DB->multiQuery($sql);
}
// Update the User with Details submitted.
$login->updateTable($column, $data, $userId, false);
// Update Subjects if yes.
if (($empty&64) > 0) {
	$sql = "DELETE FROM `teacher_subjects` WHERE `teacher_id`='". $userId ."';";
	for ($i = 0; $i < count($subjects); $i++) {
		$sql .= "INSERT INTO `teacher_subjects` (`teacher_id`, `branch`, `subject_code`) VALUES ('". $userId ."', '". $branch ."', '". $subjects[$i] ."');";
	}
	$login->DB->multiQuery($sql);
}
$login->addInfo('Details Updated Successfully');

// Insert Log for both users.
$log .= "By ". $login->name ."(". $login->id .")";
$login->logs->insertLogByType(CMSYS_LOG_UPDATE_MEMBER, array("reasona" => $log, "id" => $userId));
$login->logs->insertLogByType(CMSYS_LOG_UPDATE_MEMBER, array("reasona" => $log));
$login->redirect($redirectURL, 1);
?>