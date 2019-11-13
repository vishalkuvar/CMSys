<?php
	/**
	 * View Files Page
	 */
	// Load CSS
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/table.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/input_text.css">';
	// Check for Type
	if (count($matches) != 5)
		$login->redirect("CMSYS_PROFILE");
	// Load Type Info
	$type = $matches[4];
	switch($type) {
		case 'notice':
			$constantType = 'UPLOAD_NOTICE';
			$sRedirectURL = 'CMSYS_SYSTEM_VIEW_NOTICE';
			$redirectURL = 'CMSYS_VIEW_NOTICE';
			break;
		case 'notes':
			$constantType = 'UPLOAD_NOTES';
			$sRedirectURL = 'CMSYS_SYSTEM_VIEW_NOTES';
			$redirectURL = 'CMSYS_VIEW_NOTES';
			break;
		case 'assignment':
			$constantType = 'UPLOAD_ASSIGNMENT';
			$sRedirectURL = 'CMSYS_SYSTEM_VIEW_ASSIGNMENT';
			$redirectURL = 'CMSYS_VIEW_ASSIGNMENT';
			break;
		default:
			$login->addError("Invalid File Type");
			$login->errorRedirect("CMSYS_PROFILE");
	}
	// Show Errors.
	ErrorHandler::showError();
	ErrorHandler::showInfo();
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
	// Select All Files for user's branch/Semester
	$sql = "SELECT `files`.`id`, `file_name`, `uploaded_by`,`login`.`name`,`files`.`date` FROM `files` RIGHT JOIN `login` ON `login`.`id` = `files`.`uploaded_by` WHERE `files`.`branch`&$branchId > 0 AND `files`.`semester`&$semId > 0 AND `file_type`=". constant($constantType) ." ORDER BY `files`.`id` DESC";
	$login->DB->query($sql);
	// Output the Files.
	if ($login->DB->result->num_rows > 0) {
		// Table Header
		echo "<table border=1 width=30% class='cmsys-table'>";
		echo "	<tr>
					<th>Sr No.</th>
					<th>File Name</th>
					<th>Uploaded By</th>
					<th>Date</th>
					<th>Action</th>
				</tr>";
		$i = 1;
		// Fetch each row from SQL
		while (($res = $login->DB->result->fetch_assoc())) {
			// Save FileName and Uploader Name
			$fileNameToDisplay = $res['file_name'];
			$uploaderName = $res['name'];
			// Display them.
			echo "	<tr>
						<td>". $i++ ."</td>
						<td>$fileNameToDisplay</td>
						<td>$uploaderName</td>
						<td>". date('d-m-Y H:i:s', $res['date']) ."</td>";
?>
			<!-- Action Bar: Download(in new URL) -->
			<td>
				<input type="button" class="thick" name="download" value="Download File" onclick="<?php echo "window.open('". RedirectHandler::getRedirectURL($sRedirectURL, $res['id']) ."')"; ?>"></input><br/>
			</td>
<?php
			echo "	</tr>";
		}
		echo "</table>";
	} else {	// No File to Display
		echo "<br/><br/><center><img src='". $rootDir ."/bg/download.png' width='20%'></center>";
	}
?>