<?php 
	/**
	 * Finalize Paper Page
	 */
	// Get STudent and Paper Id
	if (isset($_POST["studId"]) && !empty($_POST["studId"])) {
		$studId = intval($_POST["studId"]);
	}
	if (isset($_POST["paperId"]) && !empty($_POST["paperId"])) {
		$paperId = intval($_POST["paperId"]);
	}
	// Error if any one is absent.
	if (!isset($studId) || !isset($paperId)) {
		$login->addError("Unable to Finalize the Paper.");
		$login->redirect("CMSYS_LIST_PAPER");
	}
	// Set checked to 2, and checked_by to Own UserID.
	$sql = "UPDATE `paper_student` SET `checked`='2', `checked_by`='". $login->id ."' WHERE `paper_id`='$paperId' AND `student_id`='$studId'";
	// Execute and redirect back
	$login->DB->query($sql);
	$login->redirect("CMSYS_LIST_PAPER_STUDENT_TEACHER", $paperId);
?>