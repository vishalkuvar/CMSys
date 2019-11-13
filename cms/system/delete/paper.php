<?php
// Check for URL
if (count($matches) != 5) {
	// Invalid URl, check if titleType is present.
	RedirectHandler::redirect('CMSYS_LIST_PAPER', 1);
}
// Save Type
$redirectURL = 'CMSYS_LIST_PAPER';
// Save Member ID.
$paperId = intval($matches[4]);
// Remove Paper from main and student.
$sql = "DELETE FROM `paper_main` WHERE `id`='$paperId'";
$login->DB->query($sql);
$sql = "DELETE FROM `paper_student` WHERE `paper_id`='$paperId'";
$login->DB->query($sql);
$login->addInfo('Paper Deleted');
// Log
$login->logs->insertLogByType(CMSYS_LOG_DELETE_PAPER, array('reasona' => 'Id: $paperId'));
$login->redirect($redirectURL);
?>