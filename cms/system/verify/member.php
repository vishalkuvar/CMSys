<?php
// Validate New User Module

// Check the User ID
if (count($matches) != 5) {
	$login->addError("Cannot Find User ID.");
	$login->errorRedirect("CMSYS_LIST_STUDENT_VERIFY");
}
// Check if User exists.
$login->DB->query("SELECT `semester` FROM `login` WHERE `verified`='1' AND `id`='". $matches[4] ."'");
if ($login->DB->result->num_rows == 0) {
	$login->addError("User does not exist or is not verified yet.");
	$login->errorRedirect("CMSYS_LIST_STUDENT_VERIFY");
}
$res = $login->DB->result->fetch_assoc();
// Check User Semester.
if ($res['semester'] != 'Sem0') {
	$login->addError("User already Validated");
	$login->errorRedirect("CMSYS_LIST_STUDENT_VERIFY");
}
// Verify them and redirect to edit student.
$login->DB->query("UPDATE `login` SET `semester`='Sem I' WHERE `id`='". $matches[4] ."'");
$login->redirect("CMSYS_EDIT_STUDENT", $matches[4]);
?>