<?php
	// Check for URL
	if (count($matches) != 6)
		$login->redirect("CMSYS_PROFILE");
	// Get Suitable Type.
	$type = $matches[4];
	switch($type) {
		case 'notice':
			$constantType = 'UPLOAD_NOTICE';
			$redirectURL = 'CMSYS_VIEW_NOTICE';
			break;
		case 'notes':
			$constantType = 'UPLOAD_NOTES';
			$redirectURL = 'CMSYS_VIEW_NOTES';
			break;
		case 'assignment':
			$constantType = 'UPLOAD_ASSIGNMENT';
			$redirectURL = 'CMSYS_VIEW_ASSIGNMENT';
			break;
		default:
			$login->addError("Invalid File Type");
			$login->errorRedirect("CMSYS_PROFILE");
	}
	// Get File ID.
	$fileId = intval($matches[5]);
	$fileType = constant($constantType);
	// Get File Details from SQL given FileID
	$sql = "SELECT `file_name`, `internal_filename` FROM `files` WHERE `id`='$fileId' AND `file_type`='$fileType'";
	$login->DB->query($sql);
	if ($login->DB->result->num_rows == 0) {	// No File Found.
		$login->addError("Invalid File Requested.");
		$login->errorRedirect($redirectURL);
	}
	// Fetch the rows.
	$res = $login->DB->result->fetch_assoc();
	$fileName = $res['internal_filename'];
	// Prepare FileHandler to output the data.
	$fileHandler = new FileHandler(constant($constantType));
	if ($fileHandler->fileExistByName($fileName) == false) {	// Check if File Exists in system.
		$login->addError("File Doesn't exist.");
		$login->errorRedirect($redirectURL);
	}
	// Get full File Name and Display Name/Extension
	$fullFileName = $fileHandler->getFullFileName($fileName);
	$displayFileName = $res['file_name'];
	// If File name is empty, set to NoName
	if (empty($displayFileName))
		$displayFileName = "NoName";
	$ext = pathinfo($fullFileName, PATHINFO_EXTENSION);
	// Send the File for Download
	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'. $displayFileName .'.'. $ext .'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($fullFileName));
    // Discard any data in output buffer and Flush headers
	ob_clean();
	flush();
	// Output the File
    readfile($fullFileName);
    // Redirect to View X Page
    $login->redirect($redirectURL);
?>