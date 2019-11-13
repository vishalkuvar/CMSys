<?php
/**
 * Get Paper Image
 */
// Check thr URL.
if (count($matches) != 6) {
	// output nothing.
} else {
	// Initialize File Handler.
	$fileHandler = new FileHandler(UPLOAD_PAPER);
	// Get StudentId/Paper Id and Page number from URL.
	$studId = intval($matches[3]);
	$paperId = intval($matches[4]);
	$pageNo = intval($matches[5]);
	// Check if udir exists.
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
?>