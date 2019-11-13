<?php
	/**
	 * View Paper(For Students) Page
	 */
	// Load CSS and JS
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/input2.css">';
	// Check the URL.
	if (count($matches) != 6) {
		$login->addError("Invalid Page number.");
		$login->errorRedirect("CMSYS_PROFILE");
	}
	// Get Page Number.
	$paperUID = intval($matches[4]);
	$pageNo = intval($matches[5]);
	
	$login->DB->query("SELECT * FROM `paper_student` WHERE `id`='$paperUID' AND `student_id`='". $login->id ."'");
	if ($login->DB->result->num_rows == 0) {
		$login->addError("Invalid Paper");
		$login->errorRedirect("CMSYS_PROFILE");
	}
	$res = $login->DB->result->fetch_assoc();
	$paperId = $res['paper_id'];
	$studId = $login->id;
	// Initialize Paper File Handler.
	$fileHandler = new FileHandler(UPLOAD_PAPER);
	// Check if Paper Exists.
	$exists = $fileHandler->deepDIR(array($paperId, $studId), false);
	if (!$exists) {
		$login->addError("Papers not Uploaded, Please Contact Staff Member.");
		$login->errorRedirect("CMSYS_PROFILE");
	}
	// Get Number of files.
	$count = $fileHandler->count();
	// Get Paper Image.
	$paper = RedirectHandler::getRedirectURL('CMSYS_GET_PAPER', $studId."/".$paperId."/".$pageNo);
	// Error if invalid page number.
	if ($pageNo <= 0 || $pageNo > $count) {
		$login->addError("Invalid Page Number");
		$login->errorRedirect("CMSYS_PROFILE");
	}
?>
<!-- Hidden Form(for Post Request) -->
<form action="" method="post" id="hidden_data">
	<input hidden type="text" name="studId" value="<?php echo $studId; ?>">
	<input hidden type="text" name="paperId" value="<?php echo $paperId; ?>">
	<input hidden type="text" name="pageNo" value="<?php echo $pageNo; ?>">
</form>
<center>
<div style="margin: 0 auto; position: relative;">
	<!-- Canvas, Centered -->
	<canvas id="myCanvas" width="1024" height="600" style="border:1px solid #d3d3d3;">
		Your browser does not support the canvas element.
	</canvas>
	<!-- Left Navigation Bar(Log/Delete) -->
	<div style="top: 0px; left: 0px; position: absolute;">
		<?php
			if ($pageNo > 1)
				echo '<i class="fa fa-arrow-left fa-3x" id="prevPage" style="display: block; margin-top: 300px;" aria-hidden="true"  onclick="selectButton(0);"></i>';
		?>
		
	</div>
	<!-- Right Navigation Bar(Marks/Input/Buttons) -->
	<div style="top: 0px; right: 0px; position: absolute;">
		<?php
			if ($pageNo < $count)
				echo '<i class="fa fa-arrow-right fa-3x" id="nextPage" style="display: block; margin-top: 300px;" aria-hidden="true" onclick="selectButton(1);"></i>';
		?>
	</div>
</div>
</center>
<script>
	// Initialize canvas and context.
	var canvas = document.getElementById("myCanvas");
	var ctx = canvas.getContext("2d");
	
	// Check if Firefox
	var isFirefox = typeof InstallTrigger !== 'undefined';

	var baseImg = null;	// Base Image(Paper)

	var paperId = <?php echo $paperUID; ?>;
	/**
	 * Constructor for CanvasLogBook.
	 * Initializes the Variables.
	 * @method CanvasLogBook
	 */
	var CanvasLogBook = function() {
	};

	/**
	 * Adds a Text
	 * @method fillText
	 * @param  {integer} text  Marks to Enter
	 * @param  {float} x     x co-ordinate.
	 * @param  {float} y     y co-ordinate
	 * @param  {string} color Color(Hex/Name)
	 */
	CanvasLogBook.prototype.fillText = function(text, x, y, color) {
		ctx.fillStyle =color;
		ctx.fillText(text, x, y);
	};

	// Initialize LogBook
	var canvasLogBook = new CanvasLogBook();
	// Font of Marks.
	ctx.font = "30px Arial";

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
	 * Prev/Next Button
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
		}
		url = '<?php echo RedirectHandler::getRedirectURL("CMSYS_VIEW_PAPER_STUDENT"); ?>'+ (paperId) +'/'+ (currentPage) +'/';
		// Send Post Request via Hidden Form.
		form.action = url;
		form.submit();
	}
</script>