<?php
	/**
	 * Paper Checking(Teacher/Admin) Page
	 */
	// Load CSS and JS
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/input2.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/dropdown.css">';
	echo '<script type="text/javascript" src="'.$rootDir.'/js/dropdown.js"></script>';
	// Check the URL.
	if (count($matches) != 5) {
		$login->addError("Invalid Page number.");
		$login->errorRedirect("CMSYS_PROFILE");
	}
	// Get Page Number.
	$pageNo = intval($matches[4]);
	if ($pageNo == 0)	// If Page number is 0, redirect to first.
		$pageNo = 1;
	// Check if studId/paperId POST is present
	if (isset($_POST["studId"]) && !empty($_POST["studId"])) {
		$studId = $_POST["studId"];
		$_SESSION["studId"] = $studId;
	}
	if (isset($_POST["paperId"]) && !empty($_POST["paperId"])) {
		$paperId = $_POST["paperId"];
		$_SESSION["paperId"] = $paperId;
	}
	// Error if atleast one of them is absent.
	if (!isset($studId) || !isset($paperId)) {
		$login->addError("Unable to Fetch Paper");
		$login->errorRedirect("CMSYS_PROFILE");
	}
	// Initialize Paper File Handler.
	$fileHandler = new FileHandler(UPLOAD_PAPER);
	// Check if Paper Exists.
	$exists = $fileHandler->deepDIR(array($paperId, $studId), false);
	if (!$exists) {
		$login->addError("Papers not Uploaded, Please Contact Staff Member.");
		$login->errorRedirect("CMSYS_LIST_PAPER");
	}
	// Get Number of files.
	$count = $fileHandler->count();
	// Get Paper Image.
	$paper = RedirectHandler::getRedirectURL('CMSYS_GET_PAPER', $studId."/".$paperId."/".$pageNo);
	// Error if invalid page number.
	if ($pageNo <= 0 || $pageNo > $count) {
		$login->addError("Invalid Page Number");
		$login->errorRedirect("CMSYS_LIST_PAPER");
	}
?>
<!-- Hidden Form(for Post Request) -->
<form action="" method="post" id="hidden_data">
	<input hidden type="text" name="studId" value="<?php echo $studId; ?>">
	<input hidden type="text" name="paperId" value="<?php echo $paperId; ?>">
	<input hidden type="text" name="pageNo" value="<?php echo $pageNo; ?>">
</form>
<center>
<!-- Status -->
<div id="controllers">
	<b id="saveStatus">Nothing to Save</b>
</div>
<div style="margin: 0 auto; position: relative;">
	<!-- Canvas, Centered -->
	<canvas id="myCanvas" width="1024" height="600" style="border:1px solid #d3d3d3;">
		Your browser does not support the canvas element.
	</canvas>
	<!-- Left Navigation Bar(Log/Delete) -->
	<div style="top: 0px; left: 0px; position: absolute;">
		<input class="cmsys-fill cmsys-button2" type="button" id="undo" value="undo"></input>
		<div class="dropdown" id="dropdown1" style="display: block">
			<button class="dropbtn">Delete</button>
			<div id="dd-content" class="dropdown-content">
			</div>
		</div>
		<?php
			echo '<input class="cmsys-fill cmsys-button2" onclick="backToList();" style="max-width: 80px; margin: 0px; margin-top: 5px;" type="button" value="Back to List" id="b1"><br/>';
			if ($pageNo > 1)
				echo '<i class="fa fa-arrow-left fa-3x" id="prevPage" style="display: block; margin-top: 160px;" aria-hidden="true"  onclick="selectButton(0);"></i>';
		?>
		
	</div>
	<!-- Right Navigation Bar(Marks/Input/Buttons) -->
	<div style="top: 0px; right: 0px; position: absolute;">
		<input class="cmsys-fill cmsys-input2" style="max-width: 80px; top: 0px; float: top;" type="text" id="marks" placeholder="Input Marks">
		<input class="cmsys-fill cmsys-input2" style="max-width: 80px; display: block; " type="text" id="color" placeholder="Color(D:red)"><br/>
		<input class="cmsys-fill cmsys-button2" onclick="setMarks(1);" style="max-width: 10px; margin: 0px;" type="button" value="1" id="s1">
		<input class="cmsys-fill cmsys-button2" onclick="setMarks(2);" style="max-width: 10px; margin: 0px;" type="button" value="2" id="s2"><br/>
		<input class="cmsys-fill cmsys-button2" onclick="setMarks(3);" style="max-width: 10px; margin: 0px; margin-top: 5px;" type="button" value="3" id="s3">
		<input class="cmsys-fill cmsys-button2" onclick="setMarks(4);" style="max-width: 10px; margin: 0px;" type="button" value="4" id="s4"><br/>
		<input class="cmsys-fill cmsys-button2" onclick="setMarks(5);" style="max-width: 10px; margin: 0px; margin-top: 5px;" type="button" value="5" id="s5">
		<input class="cmsys-fill cmsys-button2" onclick="setMarks(6);" style="max-width: 10px; margin: 0px;" type="button" value="6" id="s6"><br/>
		<input class="cmsys-fill cmsys-button2" onclick="setMarks(7);" style="max-width: 10px; margin: 0px; margin-top: 5px;" type="button" value="7" id="s7">
		<input class="cmsys-fill cmsys-button2" onclick="setMarks(8);" style="max-width: 10px; margin: 0px;" type="button" value="8" id="s8"><br/>
		<?php
			if ($pageNo < $count)
				echo '<i class="fa fa-arrow-right fa-3x" id="nextPage" style="display: block;" aria-hidden="true" onclick="selectButton(1);"></i>';
			else {
				echo '<input class="cmsys-fill cmsys-button2" onclick="finalize();" style="max-width: 80px; margin: 0px; margin-top: 5px;" type="button" value="Finalize" id="f1"><br/>';
			}
		?>
	</div>
</div>
</center>
<!--
	JQuery Already loaded from profile.php.
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
-->
<script>
	// Initialize canvas and context.
	var canvas = document.getElementById("myCanvas");
	var ctx = canvas.getContext("2d");
	// Canvas/undo Click Listener.
	canvas.addEventListener('click', on_canvas_click, false);
	document.getElementById("undo").addEventListener('click', on_undo, false);
	
	// Check if Firefox
	var isFirefox = typeof InstallTrigger !== 'undefined';

	var baseImg = null;	// Base Image(Paper)
	/**
	 * Constructor for CanvasLogBook.
	 * Initializes the Variables.
	 * @method CanvasLogBook
	 */
	var CanvasLogBook = function() {
		this.x = [];
		this.y = [];
		this.color = [];
		this.text = [];
		this.xIndex = 0;
	};

	/**
	 * Generates DropDown Menu using log.
	 * @method generateDropDown
	 * @see generateA
	 */
	CanvasLogBook.prototype.generateDropDown = function() {
		var d = document.getElementById("dd-content");
		var doc = '';
		for (var i = this.xIndex-1; i >= 0; i--) {
			doc += generateA(i, this.text[i] +"("+ this.x[i] +", "+ this.y[i] +")");
		}
		d.innerHTML = doc;
	};

	/**
	 * Adds a Text and Generates Dropdown.
	 * Also Saves the Text Log
	 * @method fillText
	 * @param  {integer} text  Marks to Enter
	 * @param  {float} x     x co-ordinate.
	 * @param  {float} y     y co-ordinate
	 * @param  {string} color Color(Hex/Name)
	 */
	CanvasLogBook.prototype.fillText = function(text, x, y, color) {
		ctx.fillStyle =color;
		ctx.fillText(text, x, y);
		this.x[this.xIndex] = x;
		this.y[this.xIndex] = y;
		this.color[this.xIndex] = color;
		this.text[this.xIndex] = text;
		this.xIndex++;
		this.generateDropDown();
		this.sendPost();
	};

	/**
	 * Removes a Particular Text Enetered
	 * @method undo
	 * @param  {int} index index to remove.
	 */
	CanvasLogBook.prototype.undo = function(index) {
		// -1 = Remove Last Inserted.
		if (index == -1)
			index = this.xIndex-1;
		// Decrease xIndex.
		this.xIndex--;
		if (this.xIndex < 0) {
			this.xIndex = 0;
			return;
		}
		// Clear the Canvas
		ctx.clearRect(0, 0, $('#myCanvas').width(), $('#myCanvas').height());
		// Draw Base Image
		if (isFirefox) {
			var image = baseImg;
			ctx.drawImage(image, 0, 0);
		} else {
			var image = new Image();
			image.src = baseImg;
			ctx.drawImage(image, 0, 0);
		}
		// If index != xIndex, the text is in middle, so shift the arrays.
		if (index != this.xIndex) {
			var remIndex = index;
			var lastIndex = this.xIndex;
			// Shift from remIndex to lastIndex
			for (var i = remIndex; i <= lastIndex; i++) {
				this.text[i] = this.text[i+1];
				this.x[i] = this.x[i+1];
				this.y[i] = this.y[i+1];
				this.color[i] = this.color[i+1];
			}
		}
		// Add the Logs.
		for (var i = 0; i < this.xIndex; i++) {
			ctx.fillStyle = this.color[i];
			ctx.fillText(this.text[i], this.x[i], this.y[i]);
		}
		// Generate DropDown and save it.
		this.generateDropDown();
		this.sendPost();
	};

	/**
	 * Sends POST Request and Saves the Marks.
	 * @method sendPost
	 */
	CanvasLogBook.prototype.sendPost = function() {
		// Get Save Status Element
		var save = document.getElementById("saveStatus");
		// Send POST via AJAX.
		$.ajax({
			type: "POST",
			url: "<?php echo RedirectHandler::getRedirectURL('CMSYS_SYSTEM_SYNC_MARKS'); ?>",
			data: { 
				text: this.text,
				x: this.x,
				y: this.y,
				color: this.color,
				maxIndex: this.xIndex-1,
				studId: '<?php echo $studId; ?>',
				paperId: '<?php echo $paperId; ?>',
				pageNo: '<?php echo $pageNo; ?>'
			},
			beforeSend: function() {
				// Status before send.
				save.innerHTML = "Saving...";
			},
			error: function() {
				// OnError Status.
				save.innerHTML = "Unable to save.";
			}
		}).done(function(o) {
			save.innerHTML = "Saved";
			console.log('saved'); 
			var x = $(o).find('#wwww').html();
			switch(x) {
				// Cannot Save
				case "-1":
					save.innerHTML = "Unable to save. Please retry again.";
					break;
				// 0 Logs, Nothing to save.
				case "-2":
					save.innerHTML = "Nothing to Save.";
					break;
				// Some unexpected behavior.
				case "-3":
					save.innerHTML = "Error. Please Contact Administrator.";
					break;
				// Successfully saved,.
				default:
					save.innerHTML = "Saved";
					return true;
					break;				
			}
		});
	}

	/**
	 * Deletes a Particular Log
	 * @method deleteLog
	 * @param  {integer}  index index to delete
	 */
	function deleteLog(index) {
		canvasLogBook.undo(index);
	}

	/**
	 * Generates <a> tag for deletion of log.
	 * @method generateA
	 * @param  {integer}  index Index of Log
	 * @param  {string}  text  Text to Show
	 * @return {string} Complate a Tag.
	 */
	function generateA(index, text) {
		var a = '<a href="#" onclick="deleteLog('+ index +')"; >'+ text +'</a>';
		return a;
	}

	// Initialize LogBook
	var canvasLogBook = new CanvasLogBook();
	
	// Undo Button
	function on_undo(ev) {
		canvasLogBook.undo(-1);
	}

	// Checks if Number.
	function isNumeric(n) {
		return !isNaN(parseFloat(n)) && isFinite(n);
	}
	
	// Font of Marks.
	ctx.font = "30px Arial";

	// When Canvas is clicked.
	function on_canvas_click(ev) {
		// Get x and y co-ordinates from canvas.
		var rect = canvas.getBoundingClientRect();
    	var x = ev.clientX - rect.left;
    	var y = ev.clientY - rect.top;
    	// Get value/color from input box.
    	var value = document.getElementById("marks").value;
    	var color = document.getElementById("color").value;

    	// No Number = 0.
		if (!isNumeric(value))
			value = 0;
		// No Color means read.
		if (color == "")
			color = "red";
		// Fill the Canvas
		canvasLogBook.fillText(value, x, y, color);
	}

	// Generates base Image
	function make_base() {
		img = new Image();
		img.src = '<?php echo $paper; ?>';
		img.onload = function(){
			ctx.drawImage(img, 0, 0, img.width,    img.height,    // source rectangle
                   			0, 0, canvas.width, canvas.height);  // destination rectangle
			baseImg = document.getElementById('myCanvas').toDataURL();
<?php
			// Get the Logs and show on canvas.
				$sql = "SELECT `x`,`y`,`color`,`text`,`count` FROM `paper_clog` WHERE `student_id`='$studId' AND `paper_id`='$paperId' AND `page_no`='$pageNo'";
				$login->DB->query($sql);
				if ($login->DB->result->num_rows > 0) {
					$res = $login->DB->result->fetch_assoc();
					$x = unserialize($res['x']);
					$y = unserialize($res['y']);
					$color = unserialize($res['color']);
					$text = unserialize($res['text']);
					$count = intval($res['count']);
					for ($i = 0; $i < $count; $i++) {
						echo "canvasLogBook.fillText(". $text[$i] .", ". $x[$i] .", ". $y[$i] .", '". $color[$i] ."');";
					}
				} 
?>
		}
	}
	// Generate Base.
	make_base();
	/**
	 * Prev/Next/Finalize/Back Button
	 * @method selectButton
	 * @param  {integer}     type Which button is pressed.
	 */
	function selectButton(type) {	// type = 0 => Prev, 1 => next
		// Get Form and Page.
		var form = document.getElementById("hidden_data");
		var currentPage = <?php echo $pageNo; ?>;
		var url;
		currentPage += 0;
		switch(type) {
			// Previous Page
			case 0:
				currentPage -= 1;
				break;
			// Next Page
			case 1:
				currentPage += 1;
				break;
			// Back to List students.
			case 3:
				url = '<?php echo RedirectHandler::getRedirectURL("CMSYS_LIST_PAPER_STUDENT_TEACHER", $paperId); ?>';
				break;
			// Finalize Paper.
			case 2:
				url = '<?php echo RedirectHandler::getRedirectURL("CMSYS_CHECK_PAPER_FINAL"); ?>';
				break;
		}
		if (type <= 1)
			url = '<?php echo RedirectHandler::getRedirectURL("CMSYS_CHECK_PAPER"); ?>'+ (currentPage) +'/';
		// Send Post Request via Hidden Form.
		form.action = url;
		canvasLogBook.sendPost();
		form.submit();
	}

	// Changes input box, when button is pressed.
	function setMarks(variable) {
		document.getElementById("marks").value = variable;
	}

	// Finalize Button
	function finalize() {
		selectButton(2);
	}

	// Back to List Button
	function backToList() {
		selectButton(3);
	}
</script>