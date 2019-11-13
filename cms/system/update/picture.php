<?php
// Allowed Image Type
$allowedTypes = array(IMAGETYPE_PNG);

$tmpName = $_FILES["profile-image-upload"]["tmp_name"];
// Get Uploaded File Image Type.
$detectedType = exif_imagetype($tmpName);
// Check if it's same as of AllowedType.
$error = !in_array($detectedType, $allowedTypes);
$check = getimagesize($tmpName);


$fileHandler = new FileHandler(UPLOAD_PICTURE);
if(!$error && $check !== false) {	
	$uploadOk = 1;
} else {
	// Not in PNG format.
	$fileHandler->addError("Please Upload file in PNG format.");
	$fileHandler->errorRedirect("CMSYS_UPDATE_PROFILE");
}
// Upload Picture with suffix ID.
$fileHandler->upload(file_get_contents($tmpName), $login->id);
// Log
$login->logs->insertLogByType(CMSYS_LOG_UPDATE_PICTURE);
$fileHandler->addInfo("Picture Changed Successfully");
$fileHandler->redirect("CMSYS_UPDATE_PROFILE");

?>