<?php
	/**
	 * Add Paper Page (1)
	 */
	// Load CSS
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/input_text.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/jquery-ui.css">';
	echo '<script src="'. $rootDir .'/js/ajax.js" type="text/javascript"></script>';
	echo '<script src="'. $rootDir .'/js/jquery-ui.js" type="text/javascript"></script>';
	echo '<script src="'. $rootDir .'/js/branch_select.js" type="text/javascript"></script>';

	// Neccessary Variables	
	$constantType = 'UPLOAD_PAPER';
	$redirectURL = 'CMSYS_ADD_PAPER';
	$filePlaceHolder = 'Enter Paper Name';
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
<form class="edit-student-form thick" method="post" action="<?php echo RedirectHandler::getRedirectURL('CMSYS_ADD_PAPER2'); ?>" enctype="multipart/form-data">
	<!-- Hidden Part, to be sent via POST -->
	<input type="hidden" name="dConstant" id="dConstant" value="<?php echo $constantType; ?>"></input>
	<input type="hidden" name="rConstant" id="rConstant" value="<?php echo $redirectURL; ?>"></input>
	<!-- Input File Name -->
	<?php addText("File Name"); ?><input required style="width: 300px" type="text" name="file_name" id="file_name" placeholder="<?php echo $filePlaceHolder; ?>"></input><br/><br/>
	<!-- Select Semester -->
	<?php addText("Semester"); ?>
	<div>
	<select name="semester[]" style="width: 300px" onchange="changeSem(this.options[this.selectedIndex].value)">
	<?php
		foreach($login->allYears as $id => $value) {
			echo '<option value="'. $id .'">'. $value[2] .'</option>';
		}
	?>
	</select>
	<br/>
	<br/>
	<!-- Select Branch -->
	<?php addText("Branch"); ?>
	<select name="branch[]" style="width: 300px" id="selBranch" onchange="changeBranch(this.options[this.selectedIndex].value)">
	<?php
		foreach($login->allBranches as $id => $value) {
			echo '<option value="'. $id .'">'. $value[0] .'</option>';
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
	<!-- Input Paper Marks -->
	<?php addText("Total Marks"); ?><input required style="width: 300px" type="text" name="marks" id="marks" value="20"></input><br/><br/>
	<input type="submit" name="submit" value="Add Paper"></input> <br/>
</form>
</center>
<script>
	/** @type {integer} SemesterID */
	var semId = 1;
	/** @type {integer} Branch ID */
	var branchId = 1;

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
			branch: branchId
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
