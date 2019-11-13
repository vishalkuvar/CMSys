<?php
	/**
	 * Add Attendance Page (1)
	 */
	// Load CSS
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/input_text.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/jquery-ui.css">';
	echo '<script src="'. $rootDir .'/js/ajax.js" type="text/javascript"></script>';
	echo '<script src="'. $rootDir .'/js/jquery-ui.js" type="text/javascript"></script>';
	echo '<script src="'. $rootDir .'/js/branch_select.js" type="text/javascript"></script>';

	// Show Error/Info
	ErrorHandler::showError();
	ErrorHandler::showInfo();

	// Adds Text with some CSS style.
	function addText($text, $multiplier = 1.0) {
		$prep = "<span><div style='min-width: ". (80*$multiplier) ."px; float: left; word-warp: break-word; display: inline-block;'>".$text .": </div></span>";
		echo $prep;
	}
	// Show Option Form
?>
<center>
<!-- Form -->
<form class="thick" method="post" action="<?php echo RedirectHandler::getRedirectURL('CMSYS_ADD_ATTENDANCE2'); ?>">
	<!-- Input Topics -->
	<?php addText("Topics"); ?>
	<textarea class="cmsys-fill cmsys-input2" type="text" name="topics" id="topics" placeholder="Topics Discussed" rows='3' style="width: 300px"></textarea><br/><br/>
	<!-- Select Semester -->
	<?php addText("Semester"); ?>
	<div>
	<select name="semester" style="width: 300px" onchange="changeSem(this.options[this.selectedIndex].value)">
	<?php
		if (($type == "notice" && (count($login->semArray)+1) == 8) || $login->title != 2)
			echo '<option value="-1" >All Semester</option>';
		$semId = 0;
		foreach($login->allYears as $id => $value) {
			if (in_array($value[2], $login->semArray) || $login->semester == $value[2] || $login->title != 2) {
				if (!$semId)
					$semId = $id;
				echo '<option value="'. $id .'">'. $value[2] .'</option>';
			}
		}
	?>
	</select>
	<br/>
	<br/>
	<!-- Select Branch -->
	<?php addText("Branch"); ?>
	<select name="branch" style="width: 300px" id="selBranch" onchange="changeBranch(this.options[this.selectedIndex].value)">
	<?php
		if ($login->title != 2)	// Teacher can only upload for own branch
			echo '<option value="-1">All Branches</option>';
		$branchId = 0;
		foreach($login->allBranches as $id => $value) {
			if ($login->title != 2 || $value[0] == $login->branch) {
				if (!$branchId)
					$branchId = $id;
				echo '<option value="'. $id .'">'. $value[0] .'</option>';
			}
		}
	?>
	</select>
	<br/>
	<br/>
	<!-- Select Date -->
	<?php addText("Date"); ?><input required='true' style="width: 300px" type='text' name='date' id='datepicker' class='cmsys-fill cmsys-input2' placeholder='31-01-2016' >
	<br/>
	<br/>
	<!-- Select Subject -->
	<?php addText("Subject"); ?>
		<select name="subject" style="width: 300px" id="selSubject">
		</select><br/><br/>
	<!-- Input Attendance -->
	<?php addText("Attendance"); ?><input style="width: 300px" type="text" name="attendance" id="attendance" value="1"></input><br/><br/>
	<input type="submit" name="submit" value="Proceed to Mark Attendance"></input> <br/>
</form>
</center>
<script>
	/** @type {integer} SemesterID */
	var semId = <?php echo $semId; ?>;
	/** @type {integer} Branch ID */
	var branchId = <?php echo $branchId; ?>;
	var subjects = [
		<?php
			for ($i = 0; $i < count($login->subjects); $i++) {
				if ($i != 0)
					echo ',';
				echo ' "'. $login->subjects[$i] .'"';
			}
		?>
					];

	/**
	 * Called when Semester is changed.
	 * Changes the subject dropdown box.
	 * @method changeSem
	 * @param  {integer}  a SemesterIndex
	 */
	function changeSem(a) {
		semId = a;
		callAjaxHelper();
	}
	/**
	 * Called when Branch is changed.
	 * Changes the subject dropdown box.
	 * @method changeBranch
	 * @param  {integer}  a BranchIndex
	 */
	function changeBranch(a) {
		branchId = a;
		callAjaxHelper();
	}
	/** Calls Asyncronous for initial list of subjects */
	callAjaxHelper();
	/**
	 * Ajax Call Helper
	 * Initializes PostData, PostURL.
	 * @method callAjaxHelper
	 */
	function callAjaxHelper() {
		var url = "<?php echo RedirectHandler::getRedirectURL('CMSYS_GET_SUBJECTS'); ?>";
		var data = { 
			sem: semId,
			branch: branchId,
			subjects: subjects
		};
		callAjax(url, data, ajaxComplete);
	}

	function ajaxComplete(o) {
		var x = $(o).find('#wwww').html();
		var sub = document.getElementById("selSubject");
		sub.innerHTML = x;
	}
	// Initialize Date Picker
	$( function() {
		$("#datepicker").datepicker();
		// Slide Dwon Animation
		$("#datepicker").datepicker("option", "showAnim", "slideDown");
		// Format: DD-MM-YY
		$("#datepicker").datepicker("option", "dateFormat", "dd-mm-yy");
		// Alternate Format: Day, DD MM YY
		$("#datepicker").datepicker("option", "altFormat", "DD, dd MM yy");
	});

</script>
