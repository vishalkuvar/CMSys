 <?php
 	echo "<center><b><div id='status'></div></b></center>";
	echo " 
			<table border=1 width=30% class='cmsys-table'>
						<tr>
							<th>Sr.No</th>
							<th>Student's Name</th>
							<th>Attendance</th>;
						</tr>
	";
	$i = 0;
	$attended = array();
	// Table
	while (($res = $login->DB->result->fetch_assoc())) {
		$i++;
		echo "<tr>";
		echo "<td>$i</td>";	// Sr No.
		echo "<td>". $res['name'] ."</td>";	// Name
		echo "<td>";
		echo '	<center>
					<div class="cmsys-switch">
	    				<input type="checkbox" name="cmsys-switch'. $i .'" class="cmsys-switch-checkbox" onchange="checkboxChanged('. $i .', '. $res['id'] .');" id="mycmsys-switch'. $i .'">
    					<label class="cmsys-switch-label" for="mycmsys-switch'. $i .'">
	        				<span class="cmsys-switch-inner"></span>
        					<span class="cmsys-switch-switch"></span>
    					</label>
					</div>
				</center>';
		$attended[$i] = $res['attended'];
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "<input type='button' class='cmsys-fill cmsys-button2' onclick='location.href = \"". RedirectHandler::getRedirectURL("CMSYS_PROFILE") ."\";' style='float: right;' value='Done'></input>";
?>
<script type="text/javascript">
	<?php
	for ($i = 1; $i <= count($attended); $i++) {
		if ($attended[$i] > 0)
			echo "document.getElementById('mycmsys-switch". $i ."').checked = true;";
	}
	?>
	var stat = document.getElementById("status");
	stat.innerHTML = "Nothing to save.";
	function checkboxChanged(i, studentId) {
		callAjaxHelper(document.getElementById('mycmsys-switch'+ i).checked, <?php echo $attendanceId; ?>, studentId);
	}
	/**
	 * Ajax Call Helper
	 * Initializes PostData, PostURL.
	 * @method callAjaxHelper
	 */
	function callAjaxHelper(attended, attendanceId, studentId) {
		stat.innerHTML = "Saving Attendance";
		var url = "<?php echo RedirectHandler::getRedirectURL('CMSYS_SAVE_ATTENDANCE'); ?>";
		var data = { 
			attended: attended,
			aId: attendanceId,
			sId: studentId
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
		switch(x) {
			case "-1":
				stat.innerHTML = "StudentID not Provided, Please Refresh the page or contact Administrator if issue persists";
				break;
			case "0":
				stat.innerHTML = "Unable to Save";
				break
			case "1":
				stat.innerHTML = "Attendance Saved Successfully";
				break;
		}
	}
</script>