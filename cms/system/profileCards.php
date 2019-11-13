<?php

/**
 * Displays Personal Information Card
 * Personal Information Includes:
 * 		Name, Semester, Subjects, Title
 *  Student Specific:
 *  	Branch
 *  (S-)Admin Specific:
 *  	Id
 * @method personalInformation
 */
function personalInformation() {
	global $login;
?>
	<!-- Personal Information Card -->
	<div class="card">
		<div class="container">
			<h4><b>Personal Information</b></h4>
			<hr class="style2">
		</div>
		<div class="card-left">
		<?php
			// Display ID to Admins
			if ($login->title >= 8) {
				addText("Id", "red", 1.0, true, true, true);
				echo "<a href='#'>". $login->id ."</a>";
				echo '<br/>';
			}
			// Display Name to All
			addText("Name", "red", 1.0, true, true, true);
			echo $login->name;
			echo '<br/>';
			// Branch and Semester are shown to Students.
			if ($login->title == 1) {
				addText("Branch", "red", 1.0, true, true, true);
				echo "<a href='#'>". $login->branch."</a>";
				echo '<br/>';
				addText("Semester", "red", 1.0, true, true, true);
				echo "<a href='#'>". $login->semester ."</a>";
				echo '<br/>';
			} else if ($login->title > 1) {
				// For Teachers, Show All Semesters and Subjects
				addText("Semesters", "red", 1.0, true, true, true);
				echo "<a href='#'>". $login->semester ."</a><br/>";
				for ($i = 0; $i < count($login->semArray); $i++) {
					if ($i != 0)
						addText("", "red", 1.0, false, true, true);
					echo "<a href='#'>". $login->semArray[$i] ."</a><br/>";
				}
				// Subjects
				addText("Subjects", "red", 1.0, true, true, true);
				for ($i = 0; $i < count($login->subjects); $i++) {
					if ($i != 0)
						addText("", "red", 1.0, false, true, true);
					echo "<a href='#'>". $login->allSubjects[array_search($login->subjects[$i], $login->allSubjects)+1] ."</a><br/>";
				}
				if (count($login->subjects) == 0) {
					echo "<a href='#'>No Subjects Assigned</a><br/>";
				}
			}
			// Title
			addText("Title", "red", 1.0, true, true, true);
			echo "<a href='#'>". RoleHandler::$titles[array_search($login->title, RoleHandler::$titles)+1] ."</a>"; 
		?>
		</div>
	</div>
<?php
}
/**
 * Displays Marks obtained in recent Papers
 * @method recentMarks
 */
function recentMarks() {
	global $login;
?>
	<!-- Recent Marks -->
	<div class="card" id="cardMarks">
		<div class="container">
			<h4><b>Recent Test Marks Information</b></h4>
			<hr class="style2">
		</div>
		<div id="subjectList" class="card-inner">
			<?php
			addText('Fetching Details', 'maroon', 3.0, false);
			?>
		</div>
	</div>
<?php
}

/**
 * Displays Recent Files Uploaded(Notice/Notes/Assignments)
 * @method recentFiles
 */
function recentFiles() {
	global $login, $limitDisplay;
	$branchId = 0; $semId = 0;
	// Convert Semester Name into SemID
	foreach($login->allYears as $id => $value) {
		if (!strcmp($value[2], $login->semester)) {
			$semId = 2<<($id-1);
			break;
		}
	}
	// Convert Branch Name to branch Code
	foreach($login->allBranches as $id => $value) {
		if (!strcmp($value[0], $login->branch)) {
			$branchId = $id;
			break;
		}
	}
	// Select x Recent Files
	$sql = "SELECT `file_type`, `id`, `file_name`, `date` FROM `files` WHERE `branch`&$branchId > 0 AND `semester`&$semId > 0 ORDER BY `id` DESC LIMIT $limitDisplay";
	$login->DB->query($sql);
?>
	<!-- Recent Files -->
	<div class="card">
		<div class="container">
			<h4><b>Recent Files</b></h4>
			<hr class="style2">
		</div>
		<div class="card-inner">
<?php
		$anyFile = false;	// Files Loaded?
		while ($res = $login->DB->result->fetch_assoc()) {
			// Load and Show Files.
			$anyFile = true;
			addText($res['file_name'], "red", 2.5, false);
			echo "(";
			addText(date("d-m-Y", $res['date']), "blue", 1.6, false);
			echo "): ";
			switch($res['file_type']) {
				case UPLOAD_NOTICE:
					$sRedirectURL = 'CMSYS_SYSTEM_VIEW_NOTICE';
					break;
				case UPLOAD_NOTES:
					$sRedirectURL = 'CMSYS_SYSTEM_VIEW_NOTES';
					break;
				case UPLOAD_ASSIGNMENT:
					$sRedirectURL = 'CMSYS_SYSTEM_VIEW_ASSIGNMENT';
					break;
			}
			echo " <a href='#' onclick='return filesClick(\"". RedirectHandler::getRedirectURL($sRedirectURL, $res['id']) ."\");'>Download</a>";
			echo '<br/>';
		}
		if (!$anyFile) {
			addText('No Files to View', 'maroon', 3.0, false);
		}
?>
		</div>
	</div>
<?php
}

/**
 * Displays the recently uploaded Files
 * (For Non-Students)
 * @method recentUploads
 */
function recentUploads() {
	global $login, $limitDisplay;
?>
	<!-- Recent Uploads -->
	<div class="card">
		<div class="container">
			<h4><b>Recent Uploads</b></h4>
			<hr class="style2">
		</div>
		<div class="card-inner">
<?php
			// Get Recent 5 Logs
			$tempDisplay = $limitDisplay+5;
			$login->DB->query("SELECT `log`, `date` FROM `log` WHERE `log_type`='". CMSYS_LOG_ADD_FILE ."' AND `user_id`='". $login->id ."' ORDER BY `id` DESC LIMIT $tempDisplay");
			$anyUpload = false;
			// Store already shown files by count.
			$filesShown = array();
			$limitCount = 0;
			$mysqlQueryResult = $login->DB->result;
			if ($mysqlQueryResult->num_rows > 0) {
				while (($res = $mysqlQueryResult->fetch_assoc()) != NULL && $limitCount < $limitDisplay) {
					// Get FileName Offset from log
					$fileTypeOffset = strpos($res['log'], "FileType: ");
					$branchOffset = strpos($res['log'], "Branch: ");
					$semOffset = strpos($res['log'], "Sem: ");
					$fileNameOffset = strpos($res['log'], "FileName: ");
					// Restrict by Branch And Semester
					$branch = substr($res['log'], $branchOffset+8, $semOffset-($branchOffset+8+4));
					$sem = substr($res['log'], $semOffset+5, $fileNameOffset-($semOffset+5+4));
					if ($branch != $login->branch || $sem != $login->semester)
						continue;
					if ($fileNameOffset !== false) {
						$anyUpload = true;
						// Generate File Name
						$fileType = substr($res['log'], $fileTypeOffset+10, $branchOffset-($fileTypeOffset+10)-2);	//-2: Comma, +10: FileType Text Length
						$fileName = substr($res['log'], $fileNameOffset+10);
						if ($fileName[strlen($fileName)-1] == ' ')
							$fileName = substr($fileName, 0, -1);	// Remove the Last Space
						// Get File Type And Redirect Link
						if (defined($fileType)) {
							if ($fileType == 'UPLOAD_NOTICE') {
								$fileType = "NT";
								$sRedirectURL = 'CMSYS_VIEW_NOTICE';
							} else if ($fileType == "UPLOAD_NOTES") {
								$fileType = "NO";
								$sRedirectURL = 'CMSYS_VIEW_NOTES';
							} else if ($fileType == "UPLOAD_ASSIGNMENT") {
								$fileType = "AS";
								$sRedirectURL = 'CMSYS_VIEW_ASSIGNMENT';
							}
						} else {
							$fileType = '';
							$sRedirectURL = '#';
						}
						// Check if File Exists.
						if (!isset($filesShown[$fileName])) {
							$filesShown[$fileName] = 0;
						}
						// Increase the Count
						$filesShown[$fileName]++;
						// Create temporary DB and use it for execution.
						$temp = $login->DB;
						$temp->query("SELECT `id` FROM `files` WHERE `file_name`='$fileName' AND `uploaded_by`='". $login->id ."'");
						if ($filesShown[$fileName] > $temp->result->num_rows)
							continue;
						$temp->freeResult();
						// Display In Card
						addText("<a class='red-link' href='". RedirectHandler::getRedirectURL($sRedirectURL) ."'>". $fileName ."</a> ", 'red', 2.0, true);
						addText(date('d-m-Y', $res['date']), 'blue', 1.6, false, false);
						addText("  (", 'red', 1.0, false, false);
						addText($fileType, 'maroon', 1.0, false, false);
						addText(")", 'red', 1.0, false, false);
						echo "<br/>";
						$limitCount++;
					}
				}
			}
			if (!$anyUpload) {
				addText('No Recent Uploads', 'maroon', 3.0, false);
			}
?>
		<div class="container">
			<h6>
				<i>
					NT: Notice, NO: Notes, AS: Assignment
				</i>
			</h6>
		</div>
		</div>
	</div>
<?php
}

/**
 * Displays Paper Specific Content:
 * Teachers/(S-)Admin:
 * 	Shows Remaining Papers to Check/Finalize.
 * Staff/(S-)Admin:
 * 	Shows Remaining Papers to Upload/Finalize.
 * @method paperChecking
 * @param  integer        $title       TitleID of User
 * @param  bool          $checkBranch Whether to check branch for filtering?
 */
function paperChecking($title, $checkBranch = true) {
	global $login, $limitDisplay;
?>
	<div class="card">
		<div class="container">
			<h4><b>Paper Checking</b></h4>
			<hr class="style2">
		</div>
		<div class="card-inner">
		<?php
			$necSearchCond = "";
			if ($title == 2) {	// Teachers
				if ($checkBranch) {
					// Branch and Subject Code should match.
					$necSearchCond .= "`pm`.`branch`='". $login->branch ."' ";
					$necSearchCond .= "AND `pm`.`subject_code` IN (";
					// Get all Subject Codes
					for ($i = 0; $i < count($login->subjects); $i++) {
						if ($i != 0)
							$necSearchCond .= ", ";	
						$necSearchCond .= "'". $login->subjects[$i] ."'";
					}
					if (count($login->subjects) == 0) {
						$necSearchCond .= "''";
					}
					$necSearchCond .= ") AND";
				}
				$searchCol = "checked";
			} else if ($title == 4) {	// Staff
				$searchCol = "uploaded";
			} else {
				$login->addError("Invalid Title");
				addText('No Remaining Paper', 'maroon', 1.0, false);
				return;
			}
			// Prepare Query
			$sql = "SELECT DISTINCT `pm`.`id`, `pm`.`name`, `pm`.`date`, `pm`.`subject_code` FROM `paper_main` AS `pm`, `paper_student` AS `ps` WHERE $necSearchCond `ps`.`paper_id`=`pm`.`id` AND `ps`.`$searchCol`<'2' ORDER BY `pm`.`id` DESC LIMIT $limitDisplay";
			$login->DB->query($sql);
			// Get Results
			if ($login->DB->result->num_rows > 0) {
				$result = $login->DB->result;
				while ($res = $result->fetch_assoc()) {
					// Get Partial Status
					for ($i = 0; $i <= 1; $i++) {
						$sql = "SELECT COUNT(`$searchCol`) AS `partial` FROM `paper_student` WHERE `$searchCol`='$i' AND `paper_id`='". $res['id'] ."'";
						$login->DB->query($sql);
						$resCount = $login->DB->result->fetch_assoc();
						$partial[$i] = $resCount['partial'];
					}
					// Get Redirect Link
					if ($title == 2) {
						$listURL = 'CMSYS_LIST_PAPER_STUDENT_TEACHER';
					} else if ($title == 4) {
						$listURL = 'CMSYS_LIST_PAPER_STUDENT_STAFF';
					} else {
						$listURL = 'CMSYS_LIST_PAPER';
					}
					// Display the Information.
					addText("<a class='red-link' href='". RedirectHandler::getRedirectURL($listURL, $res['id']) ."'>". ($login->allSubjects[array_search($res['subject_code'], $login->allSubjects)+1]) ."</a>", "red", 2.1, false);
					echo " (";
					addText($res['date'], "blue", 1.0, false, false);
					echo "): ";
					if ($title == 2)
						echo "RC: <a href='#'>". $partial[0] ."</a> PF: <a href='#'>". $partial[1] ."</a>";
					else if ($title == 4)
						echo "RU: <a href='#'>". $partial[0] ."</a> PF: <a href='#'>". $partial[1] ."</a>";
					echo "<br/>";
				}
				$result->free();
			} else {
				addText('No Remaining Paper', 'maroon', 3.0, false);
			}
		?>
		</div>
		<div class="container">
			<h6>
				<i>
<?php
					if ($title == 2)
						echo "RC: Remaining To Check";
					else
						echo "RU: Remaining to Upload";
?>
					, RF: Pending Finalization
				</i>
			</h6>
		</div>
	</div>
<?php
}
/**
 * Displays the List of Student pending Verification
 * @method pendingStudentVerify
 */
function pendingStudentVerify() {
	global $login, $titleCond, $limitDisplay;
?>
	<!-- Recent Marks -->
	<div class="card">
		<div class="container">
			<h4><b>Pending Student Verification</b></h4>
			<hr class="style2">
		</div>
		<div class="card-inner">
<?php
			RedirectHandler::getRedirectType('studentValidate');
			$sql = "SELECT `id`, `name`, `email` FROM `login` WHERE $titleCond ORDER BY `id` ASC LIMIT $limitDisplay";
			$login->DB->query($sql);
			$j = 1;
			if ($login->DB->result->num_rows == 0) {
				addText('No Verification Pending', 'maroon', 3.0, false);
			} else {
				while ($res = $login->DB->result->fetch_assoc()) {
					if ($j > 5)
						echo "<div id='Verify". ($j++) ."' idJ='1'>";
					else
						echo "<div id='Verify". ($j++) ."' idJ='0'>";
					addText($res['name'], "red", 1.5, false, false);
					echo "(";
					addText($res['email'], "blue", 3.0, false, false);
					echo "): ";
					echo "<a href='#' onclick='return verifyMember(\"". ($j-1) ."\", \"". $res['id'] ."\", \"". $res['name'] ."\", \"". $res['email'] ."\");'>Verify</a>";
					echo "</div>";
				}
			}
			echo "<input type='text' hidden id='totalVerification' value='$j'>";
?>
		</div>
	</div>
<?php
}
/**
 * Displays Recent Attendnace Uploaded Record
 * (For Non-Students)
 * @method attendanceCard
 * @param  integer         $title TitleID
 */
function attendanceCard($title) {
	global $login, $limitDisplay;
?>
	<div class="card">
		<div class="container">
			<h4><b>Recent Attendance Records</b></h4>
			<hr class="style2">
		</div>
		<div class="card-inner">
		<?php
			// Branch of Teacher and Subject should match.
			$necSearchCond = "";
			if ($title == 2) {
				$necSearchCond .= "`teacher_id`='". $login->id ."' ";
			} else {
				$login->addError("Invalid ID");
				addText('No Attendance Record', 'maroon', 1.0, false);
				return;
			}
			$sql = "SELECT `id`, `date`, `subject_code`, `attendance` FROM `attendance_main`WHERE $necSearchCond ORDER BY `id` DESC LIMIT $limitDisplay";
			$login->DB->query($sql);
			$attended = array();
			if ($login->DB->result->num_rows > 0) {
				$result = $login->DB->result;
				while ($res = $result->fetch_assoc()) {
					for ($i = 0; $i <= 1; $i++) {
						$sql = "SELECT COUNT(`attended`) AS `partial` FROM `attendance_student` WHERE `attended`='$i' AND `attendance_id`='". $res['id'] ."'";
						$login->DB->query($sql);
						$resCount = $login->DB->result->fetch_assoc();
						$attended[$i] = $resCount['partial'];
					}
					addText($login->allSubjects[array_search($res['subject_code'], $login->allSubjects)+1], "red", 2.1, false);
					echo " (";
					addText($res['date'], "blue", 1.0, false, false);
					echo "): ";
					echo "A: <a href='#'>". $attended[0] ."</a> P: <a href='#'>". $attended[1] ."</a> T: <a href='#'>". $res['attendance'] ."</a>";
					echo "<br/>";
				}
				$result->free();
			} else {
				addText('No Attendance Record', 'maroon', 3.0, false);
			}
		?>
		</div>
		<div class="container">
			<h6>
				<i>
					P: Present, A: Absent, T: Total Lectures
				</i>
			</h6>
		</div>
	</div>
<?php
}
/**
 * Shows Recent Attendance for Students
 * @method recentAttendancen]
 */
function recentAttendance() {
	global $login;
?>
	<!-- Recent Attendance -->
	<div class="card" id="cardAttendance">
		<div class="container">
			<h4><b>Recent Attendance Information</b></h4>
			<hr class="style2">
		</div>
		<div id="attendanceList" class="card-inner">
		<?php
			addText('Fetching Details', 'maroon', 3.0, false);
		?>
		</div>
	</div>
<?php
}
/**
 * Shows List of Teachers with No Subjects Assigned.
 * @method noSubjectsTeachers
 */
function noSubjectsTeachers() {
	global $login, $limitDisplay;
	$sql = "SELECT COUNT(`id`) AS `count` FROM `login` WHERE `title`='2' AND `id` NOT IN (SELECT DISTINCT `teacher_id` FROM `teacher_subjects`)";
	$login->DB->query($sql);
	$count = $login->DB->result->fetch_assoc()['count'];

	$sql = "SELECT `id`, `name` FROM `login` WHERE `title`='2' AND `id` NOT IN (SELECT DISTINCT `teacher_id` FROM `teacher_subjects`) ORDER BY `id` ASC LIMIT $limitDisplay";
	$login->DB->query($sql);
?>
	<!-- Teachers with no Subjects -->
	<div class="card" >
		<div class="container">
			<h4><b>Idle Teachers</b></h4>
			<hr class="style2">
		</div>
		<div class="card-inner">
		<?php
			if ($login->DB->result->num_rows > 0) {
				while ($res = $login->DB->result->fetch_assoc()) {
					addText("ID", "red", 1.0);
					addText("<a style='link { color: green; }' href='". RedirectHandler::getRedirectURL('CMSYS_EDIT_STAFF', $res['id']) ."'>". $res['id'] ."</a>", "maroon", 1.0, false);
					echo " (";
					addText(" Name", "red", 1.0);
					addText($res['name'], "blue", 1.0, false, false);
					echo ")";
					echo "<br/>";
				}
			}
			addText('Total Entries: '. $count, 'maroon', 3.0, false);
		?>
		</div>
	</div>
<?php
}
?>