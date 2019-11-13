<?php
	/**
	 * Add Paper(2) Page
	 * Students are listed.
	 */
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/table.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/input2.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/dropdown.css">';
	echo '<script type="text/javascript" src="'.$rootDir.'/js/dropdown.js"></script>';
	
	// Get Neccessary Variables
	$date = $_POST["date"];
	$paperName = $_POST["file_name"];
	$semester = $_POST["semester"][0];
	$branch = $_POST["branch"][0];
	$subjectCode = $_POST["subject"];
	$marks = $_POST["marks"];
	$semester = $login->allYears[$semester][2];
	// Get Branch in Words
	foreach($login->allBranches as $id => $value) {
		if ($id&$branch) {
			$branch = $value[0];
			break;
		}
	}
	// Getting Paper Main Entry
	$sql = "SELECT `id` FROM `paper_main` WHERE `date`='$date' AND `name`='$paperName' AND `semester`='$semester' AND `branch`='$branch' AND `subject_code`='$subjectCode' AND `marks`='$marks'";
	$login->DB->query($sql);
	if ($login->DB->result->num_rows > 0) {
		$res = $login->DB->result->fetch_assoc();
		$paperId = $res['id'];
	} else {	// Paper Entry doesn't exist, Insert into paper main
		$sql = "INSERT INTO `paper_main` (`date`, `name`, `semester`, `branch`, `subject_code`, `marks`) VALUES('$date', '$paperName', '$semester', '$branch', '$subjectCode', '$marks')";
		$login->DB->query($sql);
		$paperId = $login->DB->lastId();
		// Insert Query for Students(Only if paper entry is not found.)
		$sql = "INSERT INTO `paper_student` (`paper_id`, `student_id`) SELECT '$paperId', `id` FROM `login` WHERE `title` = 1 AND `semester` = '$semester' AND `branch` = '$branch'";
		$login->DB->query($sql);
		// Log
		$login->logs->insertLogByType(CMSYS_LOG_ADD_PAPER, array('reasona' => 'Id: $paperId, Sem: $semester, Branch: $branch, Date: $date, Subject: $subjectCode, marks: $marks'));
	}
	// Get all Students of that semester and branch
	$sql = "SELECT `login`.`name`, `login`.`id`,`paper_student`.`uploaded`,`paper_student`.`checked` FROM `login` RIGHT JOIN `paper_student` ON `login`.`id`=`paper_student`.`student_id` AND `paper_student`.`paper_id`='$paperId' WHERE `login`.`title` = '1' AND `login`.`semester` = '$semester' AND `login`.`branch` = '$branch'";
	$login->DB->query($sql);
	// Some Hidden input(Used in JS)
	echo "<input hidden id='semester' value='$semester'>";
	echo "<input hidden id='branch' value='$branch'>";
	echo "<input hidden id='subject' value='$subjectCode'>";
	echo "<input hidden id='paperId' value='$paperId'>";
	// Common Template
	include dirname(__FILE__) .'/../paper/include_listStaff.inc.php';
?>