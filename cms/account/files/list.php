<?php
	/**
	 * List All Attendance(In Table)
	 */
	// Import CSS
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/table.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/input2.css">';
	// Show Error
	$count = ErrorHandler::showError();

	// If No error, then proceed with showing tables.
	if (!$count) {
		// Initialize Page Query.
		$login->DB->query("SELECT `id`, `file_name`, `file_type`, `date` FROM `files` WHERE `uploaded_by`='". $login->id ."'");
?>
	<!-- Table -->
	<table class="cmsys-table" style="overflow-y: visible;">
		<!-- Heading -->
		<tr>
			<th>Sr.No</th>
			<th>Name</th>
			<th>Type</th>
			<th>Date</th>
			<th>Action</th>
		</tr>
<?php
		$i = 1;
		if ($login->DB->result->num_rows > 0) {
			while (($res = $login->DB->result->fetch_assoc()) != NULL) {
				if ($i%2 == 0)
					echo "<tr>";
				else
					echo '<tr class="odd">';
				echo "<td>$i</td>";
				echo "<td>". $res['file_name'] ."</td>";
				echo "<td>";
				switch($res['file_type']) {
					case UPLOAD_NOTICE:
						$file_type = "Notice";
						break;
					case UPLOAD_NOTES:
						$file_type = "Notes";
						break;
					case UPLOAD_ASSIGNMENT:
						$file_type = "Assignment";
						break;
				}
				echo $file_type;
				echo "</td>";
				$date = date('d-m-Y', $res['date']);
				echo "<td>". $date ."</td>";
?>
				<!-- DropDown Action Bar -->
				<td>
					<center>
<?php
						echo '<button class="cmsys-form cmsys-fill cmsys-red-button delete" nameJ="'. $res["file_name"] .'" idJ="'. $res['id'] .'" titleJ="'. $date .'">Delete File</a>';
?>
					</center>
				</td>
<?php
				echo "</tr>";
			}
		}
	}
?>
</table>
<script type="text/javascript">
	// Delete Alert Window.
	$(".delete").click(function (e) {
		var id = $(this).attr('idJ');
 		var result = deleteConfirm($(this).attr('nameJ'), $(this).attr('titleJ'));
 		if (result) {
			window.location = "<?php echo RedirectHandler::getRedirectURL('CMSYS_SYSTEM_DELETE_FILE'); ?>" + id + "/";
		}
    });

	// Confirmation Box
	function deleteConfirm(name, title) {
		var message = 'Are you sure you want to delete the Following File: \n' +
						'FileName: '+ name + '\n' +
						'Date: ' + title + '\n' +
						'Note: THE ACTION IS IRREVERSIBLE, PLEASE THINK BEFORE PERFORMING ANY ACTION';
		var result = window.confirm(message);
		return result;
	}
</script>
<?php
	echo "<br/>";
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
?>