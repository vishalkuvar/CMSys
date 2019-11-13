<?php
function sendResponse($response) {
	echo "<b id='wwww'>$response</b>";
}
function sendResponse2($response) {
	echo "<b id='wwwww'>$response</b>";
}
//sendResponse($_FILES["file"]["name"][0] . serialize($_POST));
$empty = empty($_FILES["file"]["name"][0]);
$status = intval($_POST["status"]);	// Get Status

// Initialize FileHandler
$fileHandler = new FileHandler(UPLOAD_PAPER);
// Creates the DIR for Upload.
$fileHandler->deepDIR(array($_POST["paperId"], $_POST["studId"]));
if ($status == 4) {	// Clear Finalize Status
	$sql = "UPDATE `paper_student` SET `uploaded`='1', `checked`='0' WHERE `paper_id`='". $_POST["paperId"] ."' AND `student_id`='". $_POST["studId"] ."'";
	$login->DB->query($sql);
	sendResponse("-4");
} else if ($status == 3) {	// Mark as Final.
	if ($fileHandler->count() == 0)
		sendResponse("-3");
	else {
		$sql = "UPDATE `paper_student` SET `uploaded`='2', `checked`='0' WHERE `paper_id`='". $_POST["paperId"] ."' AND `student_id`='". $_POST["studId"] ."'";
		$login->DB->query($sql);
		sendResponse("-2");
	}
} else if ($empty) {
	sendResponse("0");
} else {
	// Allowed Image Type:(default: PNG)
	$allowedTypes = array(IMAGETYPE_PNG);
	$error = false;
	// Get the Temporary name(directory) of uploaded file and number of files.
	$count = count($_FILES["file"]["name"]);
	for ($i = 0; $i < $count; $i++) {
		$tmpName = $_FILES["file"]["tmp_name"][$i];
		// Detect the mime type of file.
		$type2 = mime_content_type($tmpName);
		// Detect the Image Type of File.
		$detectedType = exif_imagetype($tmpName);
		// Check against all kinds of allowed image extension.
		$error = !in_array($detectedType, $allowedTypes);
		// Get the size of image.
		$check = getimagesize($tmpName);

		// Check for Error
		if($error || !$check !== false) {
			sendResponse2($_FILES["file"]["name"][$i] ." is not in PNG format");
			sendResponse("-1");	// Error in File, Not PNG
			$error = true;
			break;
		}
	}
	if (!$error) {
		$type2 = '.png';
		$iCount = 0;
		switch($status) {
			case 2:	// ReUpload
				$fileHandler->remove();
				break;
			case 1:	// Add More
				$iCount = $fileHandler->count()+1;
				break;
		}
		// Upload All Files.
		for ($i = 0, $j = $iCount; $i < $count; $i++, $j++) {
			$tmpName = $_FILES["file"]["tmp_name"][$i];
			$fileHandler->upload(file_get_contents($tmpName), $j+1, $type2);
		}
		// Set upload status as partial.
		switch($status) {
			case 1:
			case 2:
				$sql = "UPDATE `paper_student` SET `uploaded`='1', `checked`='0' WHERE `paper_id`='". $_POST["paperId"] ."' AND `student_id`='". $_POST["studId"] ."'";
				break;
		}
		$login->DB->query($sql);	// Upload Status to 0.
		$count = $fileHandler->count();
		// Send back number of files
		sendResponse("$count");
	}
}
?>