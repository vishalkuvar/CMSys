<?php
	/**
	 * Add Member Page
	 */
	// Load CSS
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/input_text.css">';
	// Show Error and Info
	$count = ErrorHandler::showError();
	if (!$count) {	
		if (count($matches) != 5) {
			$login->addError("Invalid URL.");
			RedirectHandler::redirect('CMSYS_PROFILE');
		}
		$type = RedirectHandler::getRedirectType($matches[4]);
		$redirectURL = 'CMSYS_LIST_'. $semiURL;
	}
	ErrorHandler::showInfo();
	function addText($text, $multiplier = 1.0) {
		$prep = "<span><div style='min-width: ". (80*$multiplier) ."px; word-warp: break-word; display: inline-block;'><b>".$text .": </b></div></span>";
		echo $prep;
	}
?>
<center>
<!-- Start Form -->
<form class="edit-student-form thick" method="post" action="<?php echo RedirectHandler::getRedirectURL('CMSYS_SYSTEM_ADD_'.$semiURL); ?>">
<!-- Display Table -->
<table cellpadding="10px">
	<!-- Profile Pic -->
	<tr bgcolor="#D9E4E6">
		<td rowspan="4">
			<div style="cursor: pointer; width:242px; height: 200px; display:inline-block;">
<?php
				echo '<img src="';
				echo $rootDir."/bg/242x200.svg";
				echo '" style="width:242px; height: 200px;"></img>';
?>
			</div>
		</td>
		<td style="width: 300px"><b>Username:</b> <input type="text" placeholder="Username" name="user"/></td>
		<td style="width: 400px"><b>Password:</b> <input type="password" name="password" value="password"></td>
	</tr>
	<!-- Name and Email -->
	<tr bgcolor="#EAF3F3">
		<td>
			<?php addText("Name"); ?>
			<input type="text" placeholder="Name" name="name"/>
		</td>
		<td>
			<?php addText("Email"); ?>
			<input type="text" placeholder="test@example.com" name="email"/>
		</td>
	</tr>
	<!-- Title -->
	<tr bgcolor="#D9E4E6">
		<td>
			<?php addText("Title"); ?>
			<select name="title">
			<?php
				for ($i = 1; $i < count(RoleHandler::$titles); $i += 2) {
					// Staff can add only student.
					if ($semiURL ==  "STUDENT" && RoleHandler::$titles[$i] == "Student")
						echo '<option value="'. RoleHandler::$titles[$i] .'">'. RoleHandler::$titles[$i] .'</option>';
					else if ($semiURL != "STUDENT" && RoleHandler::$titles[$i] != "Student") {
						echo '<option value="'. RoleHandler::$titles[$i] .'">'. RoleHandler::$titles[$i] .'</option>';
					}
				}
			?>
			</select>

		</td>
		<td></td>
	</tr>
	<!-- Branch and Semester -->
	<tr bgcolor="#EAF3F3">
		<td>
			<?php addText("Branch"); ?>
			<select name="branch">
			<?php
				foreach($login->allBranches as $id => $value) {
					echo '<option value="'. $value[0] .'">'. $value[0] .'</option>';
				}
			?>
			</select>
		<td>
			<?php addText("Semester"); ?>
			<select name="semester">
			<?php
				foreach($login->allYears as $id => $value) {
					echo '<option value="'. $value[2] .'">'. $value[2] .'</option>';
				}
			?>
			</select>
	</tr>
	<tr>
	<td></td>
	<td><center><input type="submit" name="add" value="Add Member"></center></td>
	</tr>
</table>
</form>
</center>