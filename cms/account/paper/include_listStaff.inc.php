<?php
	/**
	 * Template for List Paper(Staff/Teachers) sorted by student
	 */
		// Table
		if (isset($teacher)) {
			$tempWidth = 18;
		} else {
			$tempWidth = 23;
		}
		echo " 
				<table border=1 width=30% class='cmsys-table'>
							<tr>
								<th style='width: 8%;'>Sr.No</th>
								<th style='width: $tempWidth%;'>Student's Name</th>
								<th style='width: $tempWidth%;'>Status</th>
								<th style='width: $tempWidth%;'>Files Uploaded</th>";
		if (isset($teacher))
		echo "					<th style='width: $tempWidth%;'>Marks Obtained</th>";
		echo "					<th style='width: $tempWidth%;'>Action</th>
							</tr>
		";
		$i = 0;
		// Table
		while (($res = $login->DB->result->fetch_assoc())) {
			$canCorrect = true;	// Can correct the paper?
			$canRecheck = false;	// Can Recheck the Paper?
			$i++;	// Sr.No
			// Initialize the File Handler
			$fh = new FileHandler(UPLOAD_PAPER);
			// Check if Directory Exist
			$dirExist = $fh->deepDIR(array($paperId, $res["id"]), false);
			if ($dirExist)
				$count = $fh->count();
			// Action Text and Selected Value
			$actionText = "No Action";
			$selectValue = 0;
			// Is Upload already finalized?
			if (intval($res["uploaded"]) >= 2) {
				$actionText = "Paper Already Uploaded";
				// If teacher, check if paper is corrected.
				if (isset($teacher)) {
					// Paper Corrected and Finalized.
					if (intval($res["checked"]) >= 2) {
						$actionText = "Paper Corrected";
						$canCorrect = false;
						$canRecheck = true;
					}
					else if (intval($res["checked"]) >= 1)	// Paper Corrected but not finalized.
						$actionText = "Partial Paper Corrected";
					else 	// Paper Not Corrected.
						$actionText = "Paper Not Corrected";
				}
			} else if (intval($res["uploaded"]) >= 0) {	// Paper not uploaded
				$actionText = "Paper not uploaded";
				$canCorrect = false;
			}
			// '''Sr. No/Name'''
			echo "<tr>
					<td>$i</td>
					<td>". $res["name"] ."</td>
					<td>";
			$j = $i;
			// '''Status'''
			echo "
						<b id='status".$i."'>$actionText</b>
					</td>";
			// Number of Files in folder/
			echo "<td> <b id='fileS".$i."'>";
			if ($dirExist) {
				$count = $fh->count();
				echo "$count Files";
			} else
				echo "0 File";
			echo "</b></td>";
			if (!isset($teacher)) { // Staff
				// '''Show Action'''
				echo '<td colspan="2">';
				// If Finalized or not admin.
				if (intval($res["uploaded"]) >= 2 && $login->title < 8) {
					$j = -1;
				} else {
					// Show Form with File Upload/Action Button
					echo '<form method="post" id="form'.$j.'" >';
						echo '<input style="width: 80%;" class="cmsys-fill cmsys-button2" type="file" multiple name="file[]" id="file"></input>';
						echo '<input type="submit" hidden>';
					echo '</form>';
					echo '<input type="text" hidden id="fid'. $j .'" value="'. $res["id"] .'">';	// Student Id
					echo '<center>
							<div class="dropdown" id="dropdown'.$j.'">
								<button class="dropbtn">Action</button>
									<div class="dropdown-content">
										<a href="#" onclick="selectButton('. $j .', 1)"; >Upload More Files</a>
										<a href="#" onclick="selectButton('. $j .', 2)"; >Delete and Upload</a>
										<a href="#" onclick="selectButton('. $j .', 3)"; >Finalize</a>';
					// Admin can clear Finalize status.
					if ($login->title >= 8) {
						echo 			'<a href="#" onclick="selectButton('. $j .', 4)"; >Clear Status</a>';
					}
					echo 			'</div>
							</div>
						</center>';
				}
			} else {
				// '''Show Total Marks obtained.'''
				echo '<td>'. $res["tMarks"] .'</td><td>';
				// If cannot correct/recheck, skip the buttons.
				if (!$canCorrect && !$canRecheck) {
					$j = -1;
				} else {
					// $j = FormID.
					echo '<form method="post" id="form'.$j.'" target="checkPaper" >';
						echo '<input type="text" name="studId" hidden id="fid'. $j .'" value="'. $res["id"] .'">';	// Student ID
						echo '<input type="text" name="paperId" hidden id="pid'. $j .'" value="'. $paperId .'">';	// Paper ID
					echo '</form>';
					echo '
							<div class="dropdown" id="dropdown'.$j.'">
								<button class="dropbtn">Action</button>
									<div class="dropdown-content">';
					if (!$canRecheck)
					echo '				<a href="#" onclick="selectButton('. $j .', 1)"; >Check Paper</a>';
					if ($canRecheck)
					echo '				<a href="#" onclick="selectButton('. $j .', 1)"; >Recheck Paper</a>';
					if (!$canRecheck)
					echo '				<a href="#" onclick="selectButton('. $j .', 3)"; >Finalize</a>
									</div>
							</div>
						</center>';
				}
			}
			echo '</td>';
		}
		// Done Button => Back to Profile Page.
		echo "</table>";
		echo "<input type='button' class='cmsys-fill cmsys-button2' onclick='location.href = \"". RedirectHandler::getRedirectURL("CMSYS_PROFILE") ."\";' style='float: right;' value='Done'></input>";
		if (!isset($teacher)) {	// If Not teacher, load different javascript function.
?>
<script>
	function selectButton(id, status) {
		// Get Form/Dropdown/Status and File Status
		var form = document.getElementById("form"+ id);
		var dd = document.getElementById("dropdown"+ id);
		var fd = new FormData(form);
		var b = document.getElementById("status"+ id);
		var fS = document.getElementById("fileS"+ id);
		<?php
		if (isset($date)) {	// Check if Date is set(files/ folder use this.)
		?>
			fd.append("subject", document.getElementById("subject").value);
			fd.append("semester", document.getElementById("semester").value);
			fd.append("branch", document.getElementById("branch").value);
			fd.append("date", '<?php echo $date; ?>');
		<?php 
		}
		?>
		// Neccessary Conditions.
		fd.append("studId", document.getElementById("fid"+ id).value);
		fd.append("paperId", document.getElementById("paperId").value);		
		fd.append("status", status);
		// Send Post Request to Add the Paper,
		$.ajax({
			type: 'POST',
			url: '<?php echo RedirectHandler::getRedirectURL("CMSYS_SYSTEM_ADD_PAPER"); ?>'+ id +'/',
			data: fd,
			contentType: false,
			processData: false,
			beforeSend: function() {
				b.innerHTML = "Uploading Files";
			},
			success: function(o) {
				var x = $(o).find('#wwww').html();	// Reason
				var y = $(o).find('#wwwww').html();	// Additional Reason
				switch(x) {
					case "0":	// 0 Files
						b.innerHTML = "No Files to Upload";
						break;
					case "-1":	// Some Error
						b.innerHTML = "Error: "+ y;
						break;
					case "-2":	// Finlize Complete
						b.innerHTML = "Finalized(Contact System Administrator to Change)";
						form.style.display = "none";
						dd.style.display = "none";
						break;
					case "-3":	// No Files
						b.innerHTML = "Cannot Finalize with 0 Files";
						break;
					case "-4":	// Admin: Cleared Finalize
						b.innerHTML = "Cleared Upload Status";
						break;
					default:	// Uploaded
						b.innerHTML = "Files Uploaded";
						fS.innerHTML = x +" Files";
						break;
				}
			},
			error: function() {
				b.innerHTML = "Error, Please Contact System Administrator.";
			}
		});
		return true;
	}
</script>
<?php
	}
	else {	// Teachers can see this selectButton
?>
<script>
	function selectButton(id, status) {
		var url;
		// Form ID
		var form = document.getElementById("form"+ id);

		switch(status) {
			case 1: 	// Check
				form.action = '<?php echo RedirectHandler::getRedirectURL("CMSYS_CHECK_PAPER"); ?>1/';
				break;
			case 3: 	// Finalize
				form.action = '<?php echo RedirectHandler::getRedirectURL("CMSYS_CHECK_PAPER_FINAL"); ?>'+ id +'/';
				break;
		}
		// Submit Form(Opens in new tab)
		form.submit();
		return false;
	}
</script>
<?php
	}
?>