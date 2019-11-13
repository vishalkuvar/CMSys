<?php
	/**
	 * List All Papers(In Table)
	 */
	// Import CSS
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/table.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/dropdown.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/input2.css">';
	echo '<script type="text/javascript" src="'.$rootDir.'/js/dropdown.js"></script>';
	// Show Error
	$count = ErrorHandler::showError();

	// If No error, then proceed with showing tables.
	if (!$count) {
		if (count($matches) != 5) {
			RedirectHandler::redirect('CMSYS_LIST_PAPER', 1);
		}
		$redirectURL = 'CMSYS_LIST_PAPER';
		// Show Info
		ErrorHandler::showInfo();
		// Current/Max Page.
		$currentPage = intval($matches[4]);
		$page = max(0, $currentPage-1);
		$i = 0;

		// Search Conditions.
		$searchCond = '';
		$necSearchCond = '';
		$empty = 0;
		// Cookie is set for search conditions.
		if (isset($_COOKIE["sdate"]))
			$_SESSION["sdate"] = $_COOKIE["sdate"];;
		if (isset($_COOKIE["ssubcode"]))
			$_SESSION["ssubcode"] = $_COOKIE["ssubcode"];

		// If date is set, add searchCond for SQL
		if (isset($_SESSION["sdate"]) && !empty($_SESSION["sdate"])) {
			$searchCond .= "`date` LIKE '%". $_SESSION["sdate"] ."%' ";
			$empty = 1;
		}
		// Subject Code Search Condition.
		if (isset($_SESSION["ssubcode"]) && !empty($_SESSION["ssubcode"])) {
			if ($empty)
				$searchCond .= "AND ";
			$searchCond .= "`subject_code` LIKE '%". $_SESSION["ssubcode"] ."%' ";
			$empty = 1;
		}
		if ($login->title == 2) {	// Teacher
			// Branch of Teacher and Paper should match.
			$necSearchCond = "WHERE `branch`='". $login->branch ."' ";
			$necSearchCond .= "AND `subject_code` IN (";
			for ($i = 0; $i < count($login->subjects); $i++) {
				if ($i != 0)
					$necSearchCond .= ", ";	
				$necSearchCond .= "'". $login->subjects[$i] ."'";
			}
			if (count($login->subjects) == 0) {
				$necSearchCond .= "''";
			}
			$necSearchCond .= ") ";
		}
		if ($empty > 0)	// Search Condition Exist.
			$searchCond = "AND ". $searchCond;
		// Initialize Page Query.
		$members = $login->DB->pageQuery(array("SELECT * FROM `paper_main` $necSearchCond $searchCond", "SELECT COUNT(*) FROM `paper_main` $necSearchCond $searchCond"), $page);
		// Get Total Entries/Pages
		$totalEntries = intval($members[1][0][0]);
		$totalPages = ceil($totalEntries/10);
		// If Page is out of limit, redirect to proper page.
		if ($page >= 1 && ($members[0] == NULL || $members[0]->num_rows == 0)) {
			if (!isset($step)) 
				$step = 10;
			$page = ceil($totalEntries/$step);	// Guess the Page
			RedirectHandler::redirect($redirectURL, $page);
		}
		// Placeholder for search bar.
		$form_date = isset($_SESSION["sdate"])?": ". $_SESSION["sdate"]: "";
		$form_sub = isset($_SESSION["ssubcode"])?": ". $_SESSION["ssubcode"]: "";
?>
	<!-- Search Menu -->
	<form onsubmit="return validateSearch();" action="<?php echo RedirectHandler::getRedirectURL($redirectURL, $currentPage); ?>">
		Search:
		<input type="text" class="cmsys-fill cmsys-input2" placeholder="Date<?php echo $form_date; ?>" id="sdate">
		<input type="text" class="cmsys-fill cmsys-input2" placeholder="Subject Code<?php echo $form_sub; ?>" id="ssubcode">
		<input type="submit" class="cmsys-fill cmsys-button2">
	</form>
	<!-- Table -->
	<table class="cmsys-table" style="overflow-y: visible;">
		<!-- Heading -->
		<tr>
			<th>Sr.No</th>
			<th>Name</th>
			<th>Subject Code</th>
			<th>Date</th>
			<th>Status</th>
			<th>Action</th>
		</tr>
<?php
		// Fetch row by row
		while (($res = $members[0]->fetch_assoc()) != NULL) {
			if ($i%2 == 0)
				echo "<tr>";
			else
				echo '<tr class="odd">';
			$i++;
			// Display Name/SubjectCode/Date
			echo "<td>$i</td>";
			echo '<td>'. $res['name'] .'</td>';
			echo '<td>'. $res['subject_code'] .'</td>';
			echo '<td>'. $res['date'] .'</td>';
			echo '<td><b>';
			// Redirect URL according to title.
			switch($login->title) {
				case 4:
					$listURL = 'CMSYS_LIST_PAPER_STUDENT_STAFF';
					break;
				case 8:
				case 16:
				case 2:
					$listURL = 'CMSYS_LIST_PAPER_STUDENT_TEACHER';
					break;
				default:
					$listURL = 'CMSYS_LIST_PAPER';
					break;
			}
			// Action Text According to Title.
			if ($login->title >= 4) {
				// Show number of Pending and Partial Uploads
				$login->DB->query("SELECT `uploaded` FROM `paper_student` WHERE `uploaded`='0' AND `paper_id`='". $res['id'] ."'");
				$notUploaded = $login->DB->result->num_rows;
				$login->DB->query("SELECT `uploaded` FROM `paper_student` WHERE `uploaded`='1'  AND `paper_id`='". $res['id'] ."'");
				$notFinalized = $login->DB->result->num_rows;
				if ($notUploaded > 0)
					echo "Pending Upload: $notUploaded<br/>";
				if ($notFinalized > 0)
					echo "Partially Uploaded: $notFinalized<br/>";
				if (!$notUploaded && !$notFinalized)
					echo "All Papers Uploaded<br/>";
			}
			if ($login->title == 2 || $login->title >= 8) {
				// Show Number of Pending and Partial Corrected Papers.
				$login->DB->query("SELECT `checked` FROM `paper_student` WHERE `checked`='0' AND `paper_id`='". $res['id'] ."'");
				$notChecked = $login->DB->result->num_rows;
				$login->DB->query("SELECT `checked` FROM `paper_student` WHERE `checked`='1' AND `paper_id`='". $res['id'] ."'");
				$partChecked = $login->DB->result->num_rows;
				$login->DB->query("SELECT `checked` FROM `paper_student` WHERE `checked`='2' AND `paper_id`='". $res['id'] ."'");
				$fullChcked = $login->DB->result->num_rows;
				if ($notChecked > 0)
					echo "Pending Correction: $notChecked<br/>";
				if ($partChecked > 0)
					echo "Partially Checked: $partChecked<br/>";
				if (!$notChecked && !$partChecked)
					echo "All Papers Corrected<br/>";
			}
			echo '</b></td>';
?>
			<!-- DropDown Action Bar -->
			<td>
				<center>
					<div class="dropdown">
						<button class="dropbtn">Action</button>
						<div class="dropdown-content">
							<a href="<?php echo RedirectHandler::getRedirectURL($listURL, $res['id']); ?>">List Students</a>
<?php
							if ($login->title >= 8)	// Admin can see Staff page too.
								echo '<a href="'. RedirectHandler::getRedirectURL("CMSYS_LIST_PAPER_STUDENT_STAFF", $res['id']) .'">List Students (Staff View)</a>';
?>
							<a class="delete" nameJ="<?php echo $res["name"]; ?>" idJ="<?php echo $res["subject_code"]; ?>" titleJ="<?php echo $res['date']; ?>" href="<?php echo RedirectHandler::getRedirectURL('CMSYS_SYSTEM_DELETE_PAPER', $res['id']) ?>">Delete Paper</a>
						</div>
					</div>
				</center>
			</td>
<?php
			echo '</tr>';
		}
?>
</table>
<div style="margin: 5px; right: 30px; position: absolute">
<table cellpadding="2">
	<tr style="display: table-cell;">
	<?php
		// Page Handler.
		$start = max(1, min($totalPages, $currentPage-2));
		$end = min($totalPages, $currentPage+2);
		if ($currentPage == $totalPages) {
			$end = $currentPage;
		} else if ($currentPage == 1) {
			$start = $currentPage;
		}
		for ($i = $start; $i <= $end; $i++) {
			echo '<th><a href="'. RedirectHandler::getRedirectURL($redirectURL, $i) .'">'. $i .'</th>';
		}
	?>
	</tr>
</table>
</div>
<script type="text/javascript">
	// Delete Alert Window.
	 $(".delete").click(function (e) {
 		var result = deleteConfirm($(this).attr('nameJ'), $(this).attr('idJ'), $(this).attr('titleJ'));
        if (result == false) {
            e.preventDefault();
        };
    });

	// Confirmation Box
	function deleteConfirm(name, id, title) {
		var message = 'Are you sure you want to delete the Following Paper: \n' +
						'Name: '+ name + '\n' +
						'Subject Code: '+ id + '\n' +
						'Date: ' + title + '\n' +
						'Note: THE ACTION IS IRREVERSIBLE, PLEASE THINK BEFORE PERFORMING ANY ACTION';

		var result = window.confirm(message);
		return result;
	}

	// Validate Search and set cookie.
	function validateSearch() {
		var sname = document.getElementById("sdate");
		var sbranch = document.getElementById("ssubcode");
		document.cookie = "sdate="+ sname.value;
		document.cookie = "ssubcode="+ sbranch.value;
		return true;
	}
</script>
<?php
	echo "<br/>";
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	}
?>