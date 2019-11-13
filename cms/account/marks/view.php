<?php
	/**
	 * View Paper Marks and Paper Images
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
		for ($i = 0; $i < count($login->semArray); $i++)
			echo '<option name="'. $login->semArray[$i] .'">'. $login->semArray[$i] .'</option>';
		echo '<option name="'. $login->semester.'">'. $login->semester .'</option>';
?>
	</select>
	<br/>
	<!-- Select Subjects -->
	<?php addText("Subject", 3); ?>
	<select class="cmsys-fill cmsys-input2" onchange="getMarks(this.options[this.selectedIndex].getAttribute('uid'));" name="subject" id="selSubject">
	</select><br/><br/>
	<!-- Table, Dynamically Generated -->
	<table border=1 class='cmsys-table' id="marks">
	</table>
<script>
	// Default SemName
	/** @type {string} Semester Name */
	var semName = '<?php echo $defaultSem; ?>';
	/** @type {object} Table object */
	var marks = document.getElementById("marks");
	/** @type {string} JSON Encoded String */
	var json;
	/** @type {string} Base DIR */
	var dir = '<?php echo $rootDir; ?>';

	/**
	 * Opens the Paper in New Window
	 * @method viewPaper
	 * @param  {integer}  paperId Unique Paper ID
	 */
	function viewPaper(paperId) {
		var dir = '<?php echo RedirectHandler::getRedirectURL('CMSYS_VIEW_PAPER_STUDENT'); ?>'+ paperId +'/1/';
		window.open(dir);
	}

	/**
	 * Generates the `marks` table according the json request
	 * and it's index received
	 * @method getMarks
	 * @param  {integer} i index of 'json' whose subject is to be considered
	 */
	function getMarks(i) {
		var subject_code = json[i].subject_code;
		var options = '';
		var k = 1;
		// Table Header
		options = '	<tr>'+
						'<th>Sr No.</th>'+
						'<th>Date</th>'+
						'<th>Name</th>'+
						'<th>Checked By</th>'+
						'<th>Marks Obtained</th>'+
						'<th>Rank</th>'+
						'<th>Action</th>'+
					'</tr>';
		// Loop thorugh each subject.
		for (var j = 0; j < json.length; j++) {
			// Match only same subject
			if (json[j].subject_code == subject_code) {
				// List all test
				options += '<tr>';
				options += '<td>'+ k +'</td>';
				options += '<td>'+ json[j].date +'</td>';
				options += '<td><b>'+ json[j].name +'</b></td>';
				options += '<td><b>'+ json[j].checked_by +'</b></td>';
				options += '<td><b>'+ json[j].tmarks +'</b>/<b>'+ json[j].marks +'</b></td>';
				options += '<td>';
				if (json[i].rank <= 3) {
					options += '<center><img style="width: 100%;" src="'+ dir +'/bg/paper/'+ json[i].rank +'.png"></img></center>';
				} else {
					options += '<b>'+ json[j].rank +'</b>';
				}
				options += '</td>';
				options += 	'<td>'+
								'<input type="button" class="cmsys-fill cmsys-button2" value="See Paper" onclick="viewPaper(\''+ json[j].id +'\')">'+
							'</td>';
				options += '</tr>';
				k++;
			}
		}
		marks.innerHTML = options;
	}
	/**
	 * Lists out all Paper when Sem is changed.
	 * @method changeSem
	 * @param  {string}  a Semester Name
	 */
	function changeSem(a) {
		semName = a;
		callAjaxHelper();
	}
	callAjaxHelper();

	/**
	 * Ajax Call Helper (View Marks)
	 * Initializes PostData, PostURL.
	 * @method callAjaxHelper
	 */
	function callAjaxHelper() {
		var url = "<?php echo RedirectHandler::getRedirectURL('CMSYS_GET_MARKS'); ?>";
		var data = {
			sem: semName
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
			getMarks(0);	// First Index Subject to be fetched automatically.
		sub.innerHTML = option;
	}

</script>
