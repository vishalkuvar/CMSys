<?php
	/**
	 * Delete File Uploaded by Same User
	 */
	if (count($matches) != 5) {
		$login->addError("Invalid File ID.");
		$login->errorRedirect("CMSYS_INDEX");
	}
	// Validate if same user uploaded the file.
	$login->DB->query("SELECT `file_type`, `internal_filename` FROM `files` WHERE `uploaded_by`='". $login->id ."' AND `id`='". $matches[4] ."'");
	if ($login->DB->result->num_rows == 0) {	// Different User or ID does not exist.
		$login->addError("File Doesn't Exist");
		$login->errorRedirect("CMSYS_INDEX");
	}
	$res = $login->DB->result->fetch_assoc();
	// Open FileHandler
	switch(intval($res['file_type'])) {
		case UPLOAD_NOTICE:
			$fType = "UPLOAD_NOTICE";
			break;
		case UPLOAD_NOTES:
			$fType = "UPLOAD_NOTES";
			break;
		case UPLOAD_ASSIGNMENT:
			$fType = "UPLOAD_ASSIGNMENT";
			break;
		default:
			$fType = "UNKNOWN(". $res['file_type'] .")";
	}
	$fileHandler = new FileHandler(intval($res['file_type']));
	// Remove the File.
	if ($fileHandler->fileExistByName($res['internal_filename'])) {
		$fileHandler->removeByName($res['internal_filename']);
	}
	// Delete from MySQL
	$login->DB->query("DELETE FROM `files` WHERE `uploaded_by`='". $login->id ."' AND `id`='". $matches[4] ."'");
	// Log
	$login->logs->insertLogByType(CMSYS_LOG_REMOVE_FILE, array("reasona" => "FileType: $fType, InternalFileName: ". $res['internal_filename']));
	// Redirect Back
	RedirectHandler::redirect('CMSYS_LIST_FILES');
?>