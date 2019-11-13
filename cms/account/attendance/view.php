<?php
	/**
 	 * View Attendance
 	 */
	// CSS Files
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/input2.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/table.css">';
	echo '<script src="'. $rootDir .'/js/ajax.js" type="text/javascript"></script>';
	// Add Text with fixed gap.
	function addText($text, $multiplier = 1.0) {
		$prep = "<span><div style='min-width: ". (80*$multiplier) ."px; word-warp: break-word; display: inline-block;'><b>".$text .": </b></div></span>";
		echo $prep;
	}
	// Select Semester
	addText("Select Semester", 3);
?>
	<select class="cmsys-fill cmsys-input2" onchange="changeSem(this.options[this.selectedIndex].value);">
<?php
		$defaultSem = $login->semester;
		echo '<option name="'. $login->semester.'" selected>'. $login->semester .'</option>';
		for ($i = 0; $i < count($login->semArray); $i++)
			echo '<option name="'. $login->semArray[$i] .'">'. $login->semArray[$i] .'</option>';
?>
	</select>
	<br/>
	<!-- Select Subjects -->
	<?php addText("Subject", 3); ?>
	<select class="cmsys-fill cmsys-input2" onchange="getAttendance(this.options[this.selectedIndex].getAttribute('uid'));" name="subject" id="selSubject">
	</select><br/><br/>
	<!-- Table, Dynamically Generated -->
	<table border=1 class='cmsys-table' id="attendance">
	</table>
<script>
	// Default SemName
	/** @type {string} Semester Name */
	var semName = '<?php echo $defaultSem; ?>';
	/** @type {object} Table object */
	var attendance = document.getElementById("attendance");
	/** @type {string} JSON Encoded String */
	var json;
	/** @type {string} Base DIR */
	var dir = '<?php echo $rootDir; ?>';

	/**
	 * Generates the `attendance` table according the json request
	 * and it's index received
	 * @method getAttendance
	 * @param  {integer} i index of 'json' whose subject is to be considered
	 */
	function getAttendance(i) {
		var subject_code = json[i].subject_code;
		var options = '';
		var k = 1;
		// Table Header
		options = '	<tr>'+
						'<th>Sr No.</th>'+
						'<th>Teacher\'s Name</th>'+
						'<th>Date</th>'+
						'<th>Topics</th>'+
						'<th>Lectures Present</th>'+
						'<th>Total Lectures</th>'+
						'<th>Percentage</th>'+
					'</tr>';
		// Loop thorugh each subject.
		for (var j = 0; j < json.length; j++) {
			// Match only same subject
			if (json[j].subject_code == subject_code) {
				// List all Attendnace
				options += '<tr>';
				options += '<td>'+ k +'</td>';
				options += '<td><b>'+ json[j].added_by +'</b></td>';
				options += '<td>'+ json[j].date +'</td>';
				options += '<td>'+ nl2br(json[j].topics) +'</td>';
				if (parseInt(json[j].attended) > 0)
					json[j].attended = json[j].total_lecture;
				options += '<td><center><b>'+ json[j].attended +'</b></center></td>';
				options += '<td><center><b>'+ json[j].total_lecture +'</b></center></td>';
				options += '<td><center><b>'+ ((json[j].attended/json[j].total_lecture)*100).toFixed(2) +'</b></center></td>';
				options += '</tr>';
				k++;
			}
		}
		attendance.innerHTML = options;
	}

	/**
	 * NewLine to <br> Tag Generator
	 * @method nl2br
	 * @param  {string} str String to convert
	 * @return {string}     String with <br/> tags
	 */
	function nl2br (str) {
    	return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br/>$2');
	}
	/**
	 * Lists out all Attendance when Sem is changed.
	 * @method changeSem
	 * @param  {string}  a Semester Name
	 * @return {[type]}    [description]
	 */
	function changeSem(a) {
		semName = a;
		callAjaxHelper();
	}
	callAjaxHelper();

	/**
	 * Ajax Call Helper
	 * Initializes PostData, PostURL.
	 * @method callAjaxHelper
	 */
	function callAjaxHelper() {
		var url = "<?php echo RedirectHandler::getRedirectURL('CMSYS_SYSTEM_GET_ATTENDANCE'); ?>";
		var data = {
			sem: semName,
			user: <?php echo $login->id; ?>
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
		// Convert String to JSON
		json = eval(x)
		// Select Option
		var sub = document.getElementById("selSubject");
		// Get All List of Subjects
		var option = '';
		var subjects = [];
		for (var i = 0; i < json.length; i++) {
			if (jQuery.inArray(json[i].subject_code, subjects) == -1) {
				subjects[subjects.length] = json[i].subject_code;
				option = option +'<option name="'+ json[i].subject_code +'" uid="'+ i +'">'+ json[i].subject +'</option>'
			}
		}
		if (json.length > 0)
			getAttendance(0);	// First Index Subject to be fetched automatically.
		sub.innerHTML = option;
	}

</script>