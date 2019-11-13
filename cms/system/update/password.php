<?php
// Check if all 3 fields are provided.
$login->checkEmpty(array('old_password'), "No Username Provided");
$login->checkEmpty(array('password', 'password_confirmation'), "New Password not provided");
$login->errorRedirect('CMSYS_UPDATE_PROFILE');
// Check if both Passwords match.
if ($login->G['password'] != $login->G['password_confirmation'])
	$login->addError("Password does not match.");
$login->errorRedirect('CMSYS_UPDATE_PROFILE');
// Check whether provided old password matches with current password.
if ($login->checkPassword($login->G['old_password'])) {
	// Update Password and Session.
	$login->updateTable(array('password'), array($_POST['password']));
	// Insert Log
	$login->logs->insertLogByType(CMSYS_LOG_UPDATE_PASSWORD);
	$login->addInfo('Details Updated Successfully');
	// Redirect
	$login->redirect('CMSYS_UPDATE_PROFILE');
}
$login->addError("Incorrect Old Password Provided.");
$login->errorRedirect('CMSYS_UPDATE_PROFILE');
?>