<?php
// Check for URL
if (count($matches) != 6) {
	// Invalid URl, check if titleType is present.
	if (count($matches) == 5) {
		RedirectHandler::getRedirectType($matches[4]);
		RedirectHandler::redirect('CMSYS_LIST_'. $semiURL, 1);
	} else {
		$login->addError("Invalid UserID.");
		RedirectHandler::redirect('CMSYS_PROFILE');
	}
}
// Save Type
$type = $matches[4];
RedirectHandler::getRedirectType($type);
$redirectURL = 'CMSYS_LIST_'. $semiURL;
// Save Member ID.
$userId = intval($matches[5]);

// The User should exist.
// if type is student, only student can be deleted.
if ($type == "student" && ($title = $login->getColumnById('title', $userId, false)) != 1) {
	$login->addError("User doesn't Exist or is not a Student");
}
$login->errorRedirect($redirectURL);
// Remove Student
$sql = "DELETE FROM `login` WHERE `id`='$userId'";
$login->DB->query($sql);
$login->addInfo('User Deleted');
// Log
$login->logs->insertLogByType(CMSYS_LOG_DELETE_MEMBER, array('reasona' => 'Id: $userId, Type: $type, Title: '. RoleHandler::getTitleName($title)));
$login->redirect($redirectURL);
?>