 <?php
	/**
	 * Add Attedance(2) Page
	 * Students are listed.
	 */
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/table.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/switch.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/input2.css">';
	echo '<script type="text/javascript" src="'.$rootDir.'/js/ajax.js"></script>';
	
	// Get Neccessary Variables
	$date = $_POST["date"];
	$topics = $_POST["topics"];
	$semester = $_POST["semester"];
	$branch = $_POST["branch"];
	$subjectCode = $_POST["subject"];
	$attendance = $_POST["attendance"];
	$semester = $login->allYears[$semester][2];
	// Get Branch in Words
	foreach($login->allBranches as $id => $value) {
		if ($id&$branch) {
			$branch = $value[0];
			break;
		}
	}
	// Getting Attendance Entry
	$sql = "SELECT `id` FROM `attendance_main` WHERE `date`='$date' AND `topics`='$topics' AND `semester`='$semester' AND `branch`='$branch' AND `subject_code`='$subjectCode' AND `attendance`='$attendance' AND `teacher_id`='". $login->id ."'";
	$login->DB->query($sql);
	if ($login->DB->result->num_rows > 0) {
		$res = $login->DB->result->fetch_assoc();
		$attendanceId = $res['id'];
	} else {	// Attendance Entry doesn't exist, Insert it.
		$sql = "INSERT INTO `attendance_main` (`teacher_id`, `date`, `topics`, `semester`, `branch`, `subject_code`, `attendance`) VALUES('". $login->id ."','$date', '$topics', '$semester', '$branch', '$subjectCode', '$attendance')";
		$login->DB->query($sql);
		$attendanceId = $login->DB->lastId();
		// Insert Query for Students(Only if attendance entry is not found.)
		$sql = "INSERT INTO `attendance_student` (`attendance_id`, `student_id`) SELECT '$attendanceId', `id` FROM `login` WHERE `title` = 1 AND `semester` = '$semester' AND `branch` = '$branch'";
		$login->DB->query($sql);
		// Log
		$login->logs->insertLogByType(CMSYS_LOG_ADD_ATTENDANCE, array('reasona' => 'Id: $attendanceId, Sem: $semester, Branch: $branch, Date: $date, Subject: $subjectCode'));
	}
	// Get all Students of that semester and branch
	$sql = "SELECT `login`.`name`, `login`.`id`,`attendance_student`.`attended` FROM `login` RIGHT JOIN `attendance_student` ON `login`.`id`=`attendance_student`.`student_id` AND `attendance_student`.`attendance_id`='$attendanceId' WHERE `login`.`title` = '1' AND `login`.`semester` = '$semester' AND `login`.`branch` = '$branch'";
	$login->DB->query($sql);
	// Some Hidden input(Used in JS)
	echo "<input hidden id='semester' value='$semester'>";
	echo "<input hidden id='branch' value='$branch'>";
	echo "<input hidden id='subject' value='$subjectCode'>";
	echo "<input hidden id='attendanceId' value='$attendanceId'>";
	// Common Template
	include dirname(__FILE__) .'/add_table_template.inc.php';
?>