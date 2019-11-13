<?php
	/**
	 * Edit Member Page
	 */
	// Load CSS/JS
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/input_text.css">';
	echo '<script src="'. $rootDir .'/js/branch_select.js" type="text/javascript"></script>';
	echo '<script src="'. $rootDir .'/js/ajax.js" type="text/javascript"></script>';
	$count = ErrorHandler::showError();
	if (!$count) {	
		if (count($matches) != 6) {
			if (count($matches) == 5) {
				RedirectHandler::getRedirectType($matches[4]);
				RedirectHandler::redirect('CMSYS_LIST_'. $semiURL, 1);
			} else {
				$login->addError("Invalid UserID.");
				RedirectHandler::redirect('CMSYS_PROFILE');
			}
		}
		ErrorHandler::showInfo();
		// Get Redirect Type
		$type = RedirectHandler::getRedirectType($matches[4]);
		$redirectURL = 'CMSYS_LIST_'. $semiURL;
		$userId = intval($matches[5]);
		// Get User Info.
		$login->DB->query("SELECT * FROM `login` WHERE `id`='$userId'");
		$login->DB->errorRedirect($redirectURL, 1);
		if ($login->DB->result->num_rows == 0) {
			$login->addError("User Not Found");
			$login->errorRedirect($redirectURL, 10);
		}
		// Fetch User Detail
		$user = $login->DB->result->fetch_assoc();
	}
	$teach_sem = array($user['semester']);
	if ($semiURL != "STUDENT") {
		$login->DB->query("SELECT `semester` FROM `teacher_semesters` WHERE `teacher_id`='$userId'");
		while ($res = $login->DB->result->fetch_assoc()) {
			$teach_sem = array_merge($teach_sem, array($res['semester']));
		}
		$teach_sem = array_unique($teach_sem);
		$multiple = "multiple";
	} else {
		$multiple = "";
	}
	function addText($text, $multiplier = 1.0) {
		$prep = "<span><div style='min-width: ". (80*$multiplier) ."px; word-warp: break-word; display: inline-block;'><b>".$text .": </b></div></span>";
		echo $prep;
	}
?>
<center>
<!-- Start Form -->
<form class="edit-student-form thick" method="post" action="<?php echo RedirectHandler::getRedirectURL('CMSYS_SYSTEM_EDIT_'.$semiURL, $user['id']); ?>">
<!-- Start Table -->
<table cellpadding="10px">
	<!-- Profile Pic -->
	<tr bgcolor="#D9E4E6">
		<td rowspan="4">
			<div style="cursor: pointer; width:242px; height: 200px; display:inline-block;">
<?php
				echo '<img src="';
				$fileHandler = new FileHandler(UPLOAD_PICTURE);
				if (($image = $fileHandler->get($userId)) != NULL) {
					$base64 = 'data:image/png;base64,' . base64_encode($image);
					echo $base64;
				} else {
					echo $rootDir."/bg/242x200.svg";
				}
				echo '" style="width:242px; height: 200px;"></img>';
?>
			</div>
		</td>
		<td style="width: 300px;">
			<?php addText("Id"); ?>
			<input type="text" name="id" value="<?php echo $user['id']; ?>" readonly>
		</td>
		<td style="width: 400px">
			<?php addText("Username"); ?>
			<input type="text" value="<?php echo $user['user']; ?>" name="user"/>
		</td>
	</tr>
	<!-- Name and Email -->
	<tr bgcolor="#EAF3F3">
		<td>
			<?php addText("Name"); ?>
			<input type="text" value="<?php echo $user['name']; ?>" name="name"/>
		</td>
		<td>
			<?php addText("Email"); ?>
			<input type="text" value="<?php echo $user['email']; ?>" name="email"/>
		</td>
	</tr>
	<!-- Title(readonly) and Subjects(Not for Student) -->
	<tr bgcolor="#D9E4E6">
		<td>
			<?php addText("Title"); ?>
			<input type="text" value="<?php echo RoleHandler::getTitleName($user['title']); ?>" name="title" readonly/>
		</td>
		<td>
		<?php 
			if ($semiURL != "STUDENT") {
				addText("Subjects");
				echo '
					<select multiple name="subjects[]" id="selSubject">
					</select>
					';
			}
		?>
		</td>
	</tr>
	<!-- Branch and Semester -->
	<tr bgcolor="#EAF3F3">
		<td>
			<?php addText("Branch"); ?>
			<select name="branch" onchange="changeBranch(this.options[this.selectedIndex].id)">
			<?php
				$branchId = 1;
				foreach($login->allBranches as $id => $value) {
					$s = '';
					if ($user['branch'] == $value[0]) {
						$s = 'selected';
						$branchId = $id;
					}
					echo '<option '. $s .' value="'. $value[0] .'" id="'.$id.'">'. $value[0] .'</option>';
				}
			?>
			</select>
		</td>
		<td>
			<?php addText("Semester"); ?>
			<select <?php echo $multiple; ?> name="semester[]" onchange="changeSem(this)">
			<?php
				$semId = array();
				foreach($login->allYears as $id => $value) {
					$s = '';
					if (in_array($value[2], $teach_sem) || $user['semester'] == $value[2]) {
						$s = 'selected';
						$semId[] = $id;
					}
					echo '<option '. $s .' value="'. $value[2] .'" id="'.$id.'">'. $value[2] .'</option>';
				}
				if (empty($semId)) {
					$semId = array(1);
				}
			?>
			</select>
		</td>
	</tr>
	<!-- Save Previous Sem -->
	<tr>
		<td></td>
		<?php
			if (intval($user['title']) == 1) {
				echo '<td><center><input type="checkbox" name="prev_sem" checked>Save Previous Semester</center></td>';
			}
		?>
	</tr>
	<!-- Submit Button -->
	<tr>
		<td></td>
		<td><center><input type="submit" name="Update"></center></td>
	</tr>
	
</table>
</form>
</center>
<script>
	/** @type {integer} SemesterID */
	var semArray = <?php echo json_encode($semId); ?>;
	/** @type {integer} BranchID */
	var branchId = <?php echo $branchId; ?>;

	function getSelectValues(select) {
		var result = [];
		var options = select && select.options;
		var opt;

		for (var i=0, iLen=options.length; i<iLen; i++) {
			opt = options[i];

			if (opt.selected) {
				result.push(opt.id); /* value/text is also valid */
			}
		}
		return result;
	}

	/**
	 * Called when Semester option is changed, to fetch respective subject
	 * @method changeSem
	 * @param  {integer}  a SemesterID
	 */
	function changeSem(a) {
		semArray = getSelectValues(a);
		callAjaxHelper();
	}
	/**
	 * Called when Branch option is changed, to fetch respective subject
	 * @method changeSem
	 * @param  {integer}  a SemesterID
	 */
	function changeBranch(a) {
		branchId = a;
		callAjax();
	}

	callAjaxHelper();
	/**
	 * Ajax Call Helper
	 * Initializes PostData, PostURL.
	 * @method callAjaxHelper
	 */
	function callAjaxHelper() {
		var url = "<?php echo RedirectHandler::getRedirectURL('CMSYS_GET_SUBJECTS'); ?>";
		var data = { 
			sem: semArray,
			branch: branchId,
			autoselect: 1,
			type: 2,
			id: <?php echo $user['id']; ?>
		};
		callAjax(url, data, ajaxComplete);
	}
	/**
	 * Generates Subject Select Option when AJAX request is completed.
	 * @method ajaxComplete
	 * @param  {string}     o HTML Request Received
	 */
	function ajaxComplete(o) {
		var x = $(o).find('#wwww').html();
		var sub = document.getElementById("selSubject");
		sub.innerHTML = x;
	}
</script>