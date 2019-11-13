<?php
/**
 * Get Paper Image
 */
// Check thr URL.
if (count($matches) != 7) {
	// output nothing.
} else {
	// Initialize File Handler.
	$fileHandler = new FileHandler(UPLOAD_PAPER);
	// Get StudentId/Paper Id and Page number from URL.
	$studId = intval($matches[4]);
	$paperId = intval($matches[5]);
	$pageNo = intval($matches[6]);
	$view = false;
	if ($login->title == 1) {
		if ($studId != $login->id) {
			echo "<b>Invalid Page Request</b>";
		} else {
			$view = true;
		}
	} else if ($login->title == 2) {
		$login->DB->query("SELECT `subject_code` FROM `paper_main` WHERE `id`='$paperId'");
		if ($login->DB->result->num_rows > 0) {
			$sub = $login->DB->result->fetch_assoc();
			for ($i = 0; $i < count($login->subjects); $i++) {
				if ($sub['subject_code'] == $login->allSubjects[array_search($login->subjects[$i], $login->allSubjects)]) {
					$view = true;
					break;
				}
			}
		} 
	} else {
		$view = true;
	}
	if ($view == true) {
		// Check if dir exists.
		$dirExists = $fileHandler->deepDIR(array($paperId, $studId), false);
		if ($dirExists) {
			// Get the file contents.
			$file = $fileHandler->get($pageNo);
			if ($file == NULL) {
				// output nothing
			} else {
				// Flush and Clean current output and print the Image.
				ob_clean();
				flush();
				echo $file;
			}
		}
	}
}
?>