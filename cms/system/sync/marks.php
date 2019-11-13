<?php
// Sync Marks
// Send Response
function response($text) {
	echo "<b id='wwww'>$text</b>";
}
// Success = Is All Variables Set?
$success = true;
/**
 * Checks if Variable is not empty and set.
 * @method checkPost
 * @param  string    $var POST argument name
 * @return string         Variable Contents.
 */
function checkPost($var) {
	global $success;
	if (isset($_POST[$var]) && !empty($_POST[$var])) {
		return $_POST[$var];
	}
	// Max index can be 0(equivalent to empty.)
	if ($var == "maxIndex" && $_POST[$var] == "0")
		return 0;
	$success = false;
	response("-1");
	return "";

}
// Check all Required Variables.
$x = checkPost("x");
$y = checkPost("y");
$text = checkPost("text");
$color = checkPost("color");
$maxIndex = checkPost("maxIndex");
$studId = checkPost("studId");
$paperId = checkPost("paperId");
$pageNo = checkPost("pageNo");
if ($success && $maxIndex >= -1) {
	// Serialize the required arrays.
	$xz = serialize($x);
	$yz = serialize($y);
	$textz = serialize($text);
	$colorz = serialize($color);
	$count = $maxIndex+1;
	// Insert into log.
	$sql = "REPLACE INTO `paper_clog` (`student_id`, `paper_id`, `page_no`, `x`, `y`, `text`, `color`, `count`) VALUES 
				('$studId', '$paperId', '$pageNo', '$xz', '$yz', '$textz', '$colorz', '$count')";
	$login->DB->query($sql);

	// Select Marks and update the total marks.
	$sql = "SELECT `marks` FROM `paper_student` WHERE `student_id`='$studId' AND `paper_id`='$paperId'";
	$login->DB->query($sql);
	if ($login->DB->result->num_rows == 0) {	// Marks doesn't exist, error.
		response("-3");
	} else {
		// Fetch the row.
		$res = $login->DB->result->fetch_assoc();
		$marks = $res['marks'];
		if (empty($marks)) {	// intialize marks
			$marks = array();
		} else {	// Unserialize the marks.
			$marks = unserialize($marks);
		}
		$tMarks = 0;
		// Get Total Marks.
		for ($i = 0; $i < $count; $i++) {
			$tMarks += intval($text[$i]);
		}
		// Update Marks of current page.
		$marks[$pageNo] = $tMarks;
		// Serialize, Calculate Total Marks.
		$sMarks = serialize($marks);
		for ($i = 1, $tMarks = 0; $i <= count($marks); $i++) {
			$tMarks += $marks[$i];
		}
		// Update the Marks back to SQL.
		$sql = "UPDATE `paper_student` SET `marks`='$sMarks', `tmarks`='$tMarks' WHERE `student_id`='$studId' AND `paper_id`='$paperId'";
		echo $sql;
		$login->DB->query($sql);
		if ($maxIndex == -1) {
			// No Marks, Send Appropriate Response.
			response("-2");
		} else {
			// Set Partial Check
			$sql = "UPDATE `paper_student` SET `checked`='1' WHERE `paper_id`='$paperId' AND `student_id`='$studId'";
			$login->DB->query($sql);
			response("1");
		}
	}
} else {
	response("-1");
}
?>