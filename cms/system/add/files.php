<?php
/**
 * Add Notes/Notice/Assignment Processing
 */
$constant = "";
// Check the dConstant(Action), rConstant(Redirection Constant.)
if (isset($_POST["dConstant"]) && isset($_POST["rConstant"])) {
	$constant = $_POST['dConstant'];
	$rConstant = $_POST['rConstant'];
}
if (empty($constant) || !defined($constant) || empty($rConstant) || !defined($rConstant)) {
	$login->addError("Invalid Request");
	$login->errorRedirect("CMSYS_PROFILE");
}
// Process Semester
$semester = 0;
for ($i = 0; $i < count($_POST["semester"]); $i++) {
	$_POST["semester"][$i] = intval($_POST["semester"][$i]);
	if ($_POST["semester"][$i] == -1) {
		if (count($login->semArray)+1 != 8 && $login->title <= 2) {
			$login->addError("Invalid Semester Selected");
			$login->errorRedirect($rConstant);
		}
		$semester = PHP_INT_MAX;
		break;
	} else {
		$testSem = $login->allYears[$_POST["semester"][$i]][2];
		if ($login->title == 2 && !in_array($testSem, $login->semArray) && $testSem != $login->semester) {
			$login->addError("Invalid Semester Entered");
			$login->errorRedirect($rConstant);
		}
		$semester += 2<<($_POST["semester"][$i]-1);
	}
}
// Process Branches
$branch = 0;
for ($i = 0; $i < count($_POST["branch"]); $i++) {
	$_POST["branch"][$i] = intval($_POST["branch"][$i]);
	if ($_POST["branch"][$i] == -1) {
		if ($login->title <= 2) {
			$login->addError("Invalid Branch Selected");
			$login->errorRedirect($rConstant);
		}
		$branch = PHP_INT_MAX;
		break;
	} else {
		if ($login->allBranches[$_POST["branch"][$i]][0] != $login->branch && $login->title <= 2) {
			$login->addError("Invalid Branch Entered");
			$login->errorRedirect($rConstant);
		}
		$branch += $_POST["branch"][$i];
	}
}
// Process File Name:
$fileName = $_POST["file_name"];
if (!isset($fileName) || empty($fileName)) {
	$login->addError("Invalid File Name");
	$login->errorRedirect($rConstant);
}
$multiple = false;
if ($constant == "UPLOAD_PAPER") {
	$multiple = true;
}
// Allowed Image Type:(default: PNG)
$allowedTypes = array(IMAGETYPE_PNG);
// Get the Temporary name(directory) of uploaded file.
$tmpName = $_FILES["file"]["tmp_name"];
// Error if No Filename is found.
if (empty($tmpName)) {
	$login->addError("File not selected");
	$login->errorRedirect($rConstant);
}
// Detect the mime type of file.
$type2 = mime_content_type($tmpName);
// Detect the Image Type of File.
$detectedType = exif_imagetype($tmpName);
// Check against all kinds of allowed image extension.
$error = !in_array($detectedType, $allowedTypes);
// Get the size of image.
$check = getimagesize($tmpName);

// Initiates Notice Upload
$fileHandler = new FileHandler(constant($constant));
if(!$error && $check !== false) {	
	$uploadOk = 1;
	// No Error, Extension is .png
	$type2 = '.png';
} else {
	// Image not present, check if pdf.
	if ($type2 == "application/pdf") {
		$type2 = ".pdf";
	// Notes/Assignment support Microsoft Word.
	} else if ($constant != "UPLOAD_NOTICE" && strpos($type2, "officedocument") !== FALSE) {
		$type2 = ".docx";
	} else {
		$fileHandler->addError("Please Upload file in PNG/PDF". (($constant != "UPLOAD_NOTICE")?"/Word(DOC)":"") ." format.");
		$fileHandler->errorRedirect($rConstant);
	}
}

// Get Valid File Suffix
do {
	$id = date("YmdHis")."_". rand(1, 100000);
} while($fileHandler->fileExists($id, $type2));
$login->DB->escape($fileName);
// Insert the Notice into SQL.
$query = "INSERT INTO `files` (`file_type`, `internal_filename`, `branch`, `semester`, `uploaded_by`, `file_name`, `date`) VALUES ('". constant($constant) ."', '". $fileHandler->getFileName($id, $type2) ."', '$branch', '$semester', '". $login->id ."', '$fileName', ". time() .")";
$login->DB->query($query);
$login->DB->errorRedirect();
// Upload the File.
$fileHandler->upload(file_get_contents($tmpName), $id, $type2);
$fileHandler->addInfo("File Uploaded.");
// Logging
// Get Semester in Words
$semester .= "";
$branch .= "";
foreach($login->allYears as $id => $value) {
	if ((2<<($id-1))&$semester) {
		$semester = $value[2] .", ";
		break;
	}
}
// Get Branch in Words
foreach($login->allBranches as $id => $value) {
	if ($id&$branch) {
		$branch = $value[0] .", ";
		break;
	}
}
// Log
$login->logs->insertLogByType(CMSYS_LOG_ADD_FILE, array("reasona" => "FileType: $constant, Branch: $branch, Sem: $semester, FileName: $fileName"));
$fileHandler->redirect($rConstant);

?>