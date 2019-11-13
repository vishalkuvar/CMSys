<?php
	/**
	 * View Member Page
	 */
	// Show Error
	$count = ErrorHandler::showError();
	if (!$count) {	
		if (count($matches) != 6) {
			$login->addError("Invalid User ID");
			RedirectHandler::redirect('CMSYS_LIST_STUDENT', 1);
		}
		// Show Info
		ErrorHandler::showInfo();
		// Get the UserID
		$userId = intval($matches[5]);
		// Get User Information.
		$login->DB->query("SELECT * FROM `login` WHERE `id`='$userId'");
		$login->DB->errorRedirect("CMSYS_LIST_STUDENT", 1);
		if ($login->DB->result->num_rows == 0) {
			$login->addError("User Not Found");
			$login->errorRedirect("CMSYS_LIST_STUDENT", 10);
		}
		$user = $login->DB->result->fetch_assoc();
	}
	function addText($text, $multiplier = 1.0) {
		$prep = "<span><div style='min-width: ". (80*$multiplier) ."px; word-warp: break-word; display: inline-block;'><b>".$text .": </b></div></span>";
		echo $prep;
	}
?>
<center>
<!-- Start Table -->
<table cellpadding="10px">
	<tr bgcolor="#D9E4E6">
		<td rowspan="4">
			<!-- Profile Pic -->
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
		<td style="width: 300px">
<?php
			addText("Id");
			echo $user['id'];
?>
		</td>
		<td></td>
	</tr>
	<!-- Name and Email -->
	<tr bgcolor="#EAF3F3">
		<td style="width: 300px">
<?php
			addText("Name");
			echo $user['name'];
?>
		</td>
		<td style="width: 300px">
<?php
			addText("Email");
			echo $user['email'];
?>
		</td>
	</tr>
	<!-- Title -->
	<tr bgcolor="#D9E4E6">
		<td>
<?php
			addText("Title");
			echo RoleHandler::getTitleName($user['title']);
?>
		</td>
		<td></td>
	</tr>
	<!-- Branch and Semester -->
	<tr bgcolor="#EAF3F3">
		<td>
<?php
			addText("Branch");
			echo $user['branch'];
?>
		</td>
		<td>
<?php
			addText("Sem");
			echo $user['semester'];
?>
		</td>
	</tr>
	
</table>
</center>