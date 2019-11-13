<?php
	/**
	 * Upload Files
	 */
	// Load CSS
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/input_text.css">';
	if (count($matches) < 5)
		$login->redirect("CMSYS_PROFILE");
	// Check Type
	$type = $matches[4];
	// File Place Holder.
	$filePlaceHolder = "Enter File Name";
	$multiple = "";
	// Type of Request
	switch($type) {
		case 'notice':
			$constantType = 'UPLOAD_NOTICE';
			$sRedirectURL = 'CMSYS_SYSTEM_ADD_NOTICE';
			$redirectURL = 'CMSYS_ADD_NOTICE';
			$multiple = "multiple";
			break;
		case 'notes':
			$constantType = 'UPLOAD_NOTES';
			$sRedirectURL = 'CMSYS_SYSTEM_ADD_NOTES';
			$redirectURL = 'CMSYS_ADD_NOTES';
			break;
		case 'assignment':
			$constantType = 'UPLOAD_ASSIGNMENT';
			$sRedirectURL = 'CMSYS_SYSTEM_ADD_ASSIGNMENT';
			$redirectURL = 'CMSYS_ADD_ASSIGNMENT';
			break;
		default:
			$login->addError("Invalid File Type");
			$login->errorRedirect("CMSYS_PROFILE");
	}
	// Show Error/Info
	ErrorHandler::showError();
	ErrorHandler::showInfo();

	// Adds Text with CSS Style
	function addText($text, $multiplier = 1.0) {
		$prep = "<span><div style='min-width: ". (80*$multiplier) ."px; float: left; word-warp: break-word; display: inline-block;'>".$text .": </div></span>";
		echo $prep;
	}
	// Show Option Form
?>
<form class="edit-student-form thick" method="post" action="<?php echo RedirectHandler::getRedirectURL($sRedirectURL); ?>" enctype="multipart/form-data">
	<center>
	<!-- Hidden POST Request -->
	<input style="width: 300px" type="hidden" name="dConstant" id="dConstant" value="<?php echo $constantType; ?>"></input>
	<input style="width: 300px" type="hidden" name="rConstant" id="rConstant" value="<?php echo $redirectURL; ?>"></input>
	<!-- Input File Name -->
	<?php addText("File Name"); ?> <input required style="width: 300px" type="text" name="file_name" id="file_name" placeholder="<?php echo $filePlaceHolder; ?>"></input></center><br/>
	<!-- Select Semester -->
	<?php addText("Semester"); ?>
	<center>
		<select <?php echo $multiple; ?> name="semester[]"  style="width: 300px">
<?php
			
			if (($type == "notice" && count($login->semArray) == 8) || $login->title != 2)
				echo '<option value="-1" >All Semester</option>';
			foreach($login->allYears as $id => $value) {
				if (in_array($value[2], $login->semArray) || $value[2] == $login->semester || $login->title != 2)
					echo '<option value="'. $id .'">'. $value[2] .'</option>';
			}
?>
		</select>
		<br/>
		<br/>
	</center>
	<!-- Select Branch -->
	<?php addText("Branch"); ?>
	<center>
		<select <?php echo $multiple; ?> name="branch[]" id="selBranch"  style="width: 300px">
<?php
			if ($login->title != 2)	// Teacher can only upload for own branch
				echo '<option value="-1">All Branches</option>';
			foreach($login->allBranches as $id => $value) {
				if ($login->title != 2 || $value[0] == $login->branch)
					echo '<option value="'. $id .'">'. $value[0] .'</option>';
			}
?>
		</select>
	</center>
	<br/>
	<br/>
	<!-- Select File to Upload -->
	<?php addText("Select File"); ?>
	<center>
		<input required type="file" name="file" id="file"></input> <br/><br/><br/>
		<input type="submit" name="submit" value="Upload File"></input> <br/>
	</center>
</form>
<br/>
<br/>