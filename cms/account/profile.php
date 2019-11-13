<?php
	include dirname(__FILE__).'/../system/profileCards.php';
	/**
	 * Home Page
	 */
	echo '<script src="'. $rootDir .'/js/ajax.js" type="text/javascript"></script>';
	ErrorHandler::showError();
	ErrorHandler::showInfo();

	/**
	 * Adds Text with Alignment and Color, with roboto font.
	 * @method addText
	 * @param  string  $text       Text to Display
	 * @param  string  $color      Color to display(Hex Code with '#' or color name)
	 * @param  float   $multiplier MinWidth Multiplier
	 * @param  bool    $semiColon  Insert Colon?
	 * @param  bool    $format     Add Span and Div format?(to give width)
	 * @param  bool    $enforce    Use Multiplier?
	 */
	function addText($text, $color = "black", $multiplier = 1.0, $semiColon = true, $format = true, $enforce = false) {
		$prep = "";
		$minWidth = 20;
		$multiplier *= 4;
		if ($enforce) {
			$minWidth *= $multiplier;
		}
		if ($format) {
			$prep .= "<span>".
						"<div class='text-align' style='min-width: ". $minWidth ."px; max-width: ". (20*$multiplier) ."px;'>";
		}
		$prep .=			"<font color='$color' class='text-font'>".
								"<b>".
									$text;
		if ($semiColon)
			$prep .=			": ";
		$prep .=			"</b>".
						"</font>";
		if ($format) {
		$prep .=		"</div>".
					"</span>";
		}
		echo $prep;
	}
?>
<div id="profile">
</div>
<div class="card-wrapper" >
	<div class="card-row">
<?php
		/**
		 * Arrangement:
		 * Row 1:
		 * 		[Info:all] [Marks:student] [Files:student] [paperChecking:teacher,staff] [recentUploads:allExceptStudent] [VerifyStudent:(s-)admin]
		 * Row 2:
		 * 		[paperChecking-Teacher:(s-)admin] [paperChecking-Staff:(s-)admin] [attendance:Teacher] [verifyStudent:staff] [recentAttendance:student] [idleTeachers:s-admin]
		 */
		/**
		 * Row 1:
		 * 		Student: 3
		 *   	Teacher: 3
		 *    	OfficeStaff: 3
		 *     	Admin: 3
		 *      Superadmin: 3
		 */
		personalInformation();	// Personal Info Card
		// For Students: Show Test Marks and Recent Files.
		if ($login->title == 1) {
			recentMarks();
			recentFiles();
		} else if ($login->title == 2 || $login->title == 4) {
			paperChecking($login->title);
		}
		if ($login->title >= 2) {
			recentUploads();
		}
		if ($login->title >= 8) {
			pendingStudentVerify();
		}
		
?>
	</div>
	<div class="card-row">
<?php
		/**
		 * Row 2:
		 * 		Student: 1
		 *   	Teacher: 1
		 *    	OfficeStaff: 1
		 *     	Admin: 2
		 *      Superadmin: 3
		 */
		if ($login->title >= 8) {
			paperChecking(2, false);
			paperChecking(4);
		}
		if ($login->title == 2) {
			attendanceCard($login->title);
		} else if ($login->title == 4) {
			pendingStudentVerify();
		} else if ($login->title == 1) {
			recentAttendance();
		}
		if ($login->title == 16) {	// Super Admin
			noSubjectsTeachers();
		}
?>
	</div>
</div>
<script type="text/javascript">
	function generateText(text, color, multiplier) {
		if (multiplier == undefined)
			multiplier = 1.0;
		multiplier *= 4;
		var res = "<span>"+
					"<div class='text-align' style='min-width: 40px; max-width: "+ (20*multiplier) +"px'>"+
						"<font color='"+ color +"' class='text-font'>"+
							"<b>"+
								text+
							"</b>"+
						"</font>"+
					"</div>"+
				"</span>";
		return res;
	}
<?php
	// Load JavaScript for Students
	if ($login->title == 1) {
?>
	function filesClick(url) {
		window.open(url);
		return false;
	}

	var json = '', json_attend = '';
	/**
	 * Ajax Call Helper
	 * Initializes PostData, PostURL.
	 * @method callAjaxHelper
	 */
	function callAjaxHelper() {
		// View Marks
		var url = "<?php echo RedirectHandler::getRedirectURL('CMSYS_GET_MARKS'); ?>";
		var data = {
			sem: '<?php echo $login->semester; ?>'
		};
		callAjax(url, data, ajaxComplete);
		// Attendance
		var url = "<?php echo RedirectHandler::getRedirectURL('CMSYS_SYSTEM_GET_ATTENDANCE'); ?>";
		var data = {
			sem: '<?php echo $login->semester; ?>',
			user: <?php echo $login->id; ?>
		};
		callAjax(url, data, ajaxCompleteAttendance);
	}
	callAjaxHelper();

	/**
	 * Generates Subject Select Option when AJAX request is completed.
	 * @method ajaxComplete
	 * @param  {string}     o HTML Request Received
	 */
	function ajaxCompleteAttendance(o) {
		var x = $(o).find('#wwww').html();
		var j = 0;
		// Is there any attendance?
		var generated = false;
		// Convert String to JSON
		json_attend = eval(x)
		// Generate Text
		var option = '';
		var subjects = {};
		// Loop through all attendance record, and group it by subject.
		for (var i = 0; i < json_attend.length; i++) {
			if (typeof subjects[json_attend[i].subject] == 'undefined') {
				subjects[json_attend[i].subject] = {};
				subjects[json_attend[i].subject]['total_lecture'] = 0;
				subjects[json_attend[i].subject]['attended'] = 0;
			}
			if (parseInt(json_attend[i].attended) > 0)
				json_attend[i].attended = json_attend[i].total_lecture;
			subjects[json_attend[i].subject]['total_lecture'] += parseInt(json_attend[i].total_lecture);
			subjects[json_attend[i].subject]['attended'] += parseInt(json_attend[i].attended);
			generated = true;
		}
		// Generate Format for showing attendance.
		for (var index in subjects) { 
			var item = subjects[index]; 
			option += generateText(index, "red", 3.0)+': ';
			option += generateText('<b>' + item['attended'] + '</b>/<b>'+ item['total_lecture'] +'</b>', "#009900");
			option += generateText('('+ Math.ceil((item['attended']/item['total_lecture'])*100) +'%)', "#000066");
			option += '<br/>';
		};
		if (generated) {
			document.getElementById("attendanceList").innerHTML = option;
		} else {
			document.getElementById("attendanceList").innerHTML = generateText('No Attendance Uploaded', 'maroon', 3.0);
		}
	}
	/**
	 * Generates Subject Select Option when AJAX request is completed.
	 * @method ajaxComplete
	 * @param  {string}     o HTML Request Received
	 */
	function ajaxComplete(o) {
		var x = $(o).find('#wwww').html();
		var j = 0;
		var generated = false;
		// Convert String to JSON
		json = eval(x)
		// Generate Text
		var option = '';
		for (var i = 0; i < json.length && j < <?php echo $limitDisplay; ?>; i++, j++) {
			generated = true;
			option += generateText(json[i].subject, "red", 3.0);
			option += '('+ generateText(json[i].date, "blue", 1.6) +'):';
			option += '<b>' + json[i].tmarks + '</b>/<b>'+ json[i].marks +'</b>';
			option += generateText('(Rank: '+ json[i].rank +')', "#000066");
			option += '<br/>';
		}
		if (generated) {
			document.getElementById("subjectList").innerHTML = option;
		} else {
			document.getElementById("subjectList").innerHTML = generateText('No Paper Uploaded', 'maroon', 3.0);
		}
	}
<?php
	} else if ($login->title == 4) {
?>
	var totalVerify = document.getElementById("totalVerification").value-1;
	var maxShown = <?php echo $limitDisplay; ?>;
	var currentShown = maxShown;
	console.log("MaxShown: "+ maxShown);
	if (totalVerify < maxShown)
		currentShown = totalVerify;

	function verifyMember(divId, id, name, email) {
		var result = deleteConfirm(name, email);
		if (result == false) {
		    return false;
		}
		var url = '<?php echo RedirectHandler::getRedirectURL('CMSYS_SYSTEM_STUDENT_VERIFY'); ?>'+ id +'/';
		currentShown--;
		document.getElementById("Verify"+ divId).style.visibility = "hidden";
		window.open(url);
		for (var i = 1; i <= totalVerify && currentShown < maxShown; i++) {
			/**
			 * Hidden ID
			 * 0 = Not Verified
			 * 1 = Hidden, Not Verified
			 * 2 = Hidden, Verified
			 * @type {integer}
			 */
			var line = document.getElementById("Verify"+ i);
			var idj = line.getAttribute("idJ");
			switch(idj) {
				case 0:
					continue;
				case 1:
					line.style.visibility = "visible";
					currentShown++;
					continue;
				case 2:
					continue;
			}
		}
		if (currentShown == 0) {
			document.getElementById("Verify1").innerHTML = generateText("No Verification Pending", "maroon", 3);
			document.getElementById("Verify"+ divId).style.visibility = "visible";
		}
		return true;
	}
	function deleteConfirm(name, email) {
		var message = 'Are you sure you want to Verify the Following user: \n' +
						'Name: '+ name + '\n' +
						'Email: '+ email + '\n' +
						'Note: THIS ACTION IS IRREVERSIBLE, PLEASE THINK BEFORE PERFORMING ANY ACTION';

		var result = window.confirm(message);
		return result;
	}
<?php
	}
?>
</script>