<!-- NOTE: DELETE IT, NOT USED. -->
<div id="controllers">
	<input type="button" id="save" value="save"></input>  
	<input type="button" id="undo" value="undo"></input>  
	<input type="button" id="redo" value="redo"></input>
	<input type="text" id="marks">
</div>
<canvas id="myCanvas" width="800" height="768" style="border:1px solid #d3d3d3;">
	Your browser does not support the canvas element.
</canvas>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>
	var canvas = document.getElementById("myCanvas");
	var ctx = canvas.getContext("2d");
	canvas.addEventListener('click', on_canvas_click, false);
	document.getElementById("undo").addEventListener('click', on_undo, false);
	document.getElementById("redo").addEventListener('click', on_redo, false);
	document.getElementById("save").addEventListener('click', on_save, false);
	
	var isFirefox = typeof InstallTrigger !== 'undefined';
	var x = [];
	var y = [];
	var value = [];
	var color = [];
	var index
	var CanvasLogBook = function() {
		this.index = 0;
		this.logs = [];
		this.logDrawing();
		this.x = [];
		this.y = [];
		this.value = [];
		this.color = [];
	};
	CanvasLogBook.prototype.sliceAndPush = function(imageObject) {
		var array;
		if (this.index == this.logs.length-1) {
			this.logs.push(imageObject);
			array = this.logs;
		} else {
			var tempArray = this.logs.slice(0, this.index+1);
			tempArray.push(imageObject);
			array = tempArray;
		}
		if (array.length > 1) {
			this.index++;
		}
		return array;
	};
	CanvasLogBook.prototype.logDrawing = function() { 
		if (isFirefox) {
			var image = new Image();
			image.src = document.getElementById('myCanvas').toDataURL();
			this.logs = this.sliceAndPush(image);
		} else {
			var imageData = document.getElementById('myCanvas').toDataURL();
			this.logs = this.sliceAndPush(imageData);
		}
	};
	CanvasLogBook.prototype.undo = function() {
		console.log(this.index);
		if (this.index == 2)
			return;
		else if (this.index > 1) {
			ctx.clearRect(0, 0, $('#myCanvas').width(), $('#myCanvas').height());
			this.index--;
			this.showLogAtIndex(this.index);
		}
	};
	CanvasLogBook.prototype.redo = function() {
		if (this.index < this.logs.length-1) {
			ctx.clearRect(0, 0, $('#myCanvas').width(), $('#myCanvas').height());
			this.index++;
			this.showLogAtIndex(this.index);
		}
	};
	CanvasLogBook.prototype.showLogAtIndex = function(index) {
		ctx.clearRect(0, 0, $('#myCanvas').width(), $('#myCanvas').height());
		if (isFirefox) {
			var image = this.logs[index];
			ctx.drawImage(image, 0, 0);
		} else {
			var image = new Image();
			image.src = this.logs[index];
			ctx.drawImage(image, 0, 0);
		}
	};
	var canvasLogBook = new CanvasLogBook();
	canvasLogBook.logDrawing();
	
	function on_undo(ev) {
		canvasLogBook.undo();
	}
	function on_redo(ev) {
		canvasLogBook.redo();
	}
	
	ctx.font = "30px Arial";
	function on_canvas_click(ev) {
		var value = document.getElementById("marks").value;
		var x = ev.clientX - canvas.offsetLeft;
		var y = ev.clientY - canvas.offsetTop;
		if (value == "" || value == "undefined")
			return;
		console.log("Value: "+ value);
		ctx.strokeText(document.getElementById("marks").value, x, y);
		canvasLogBook.logDrawing();
	}
	function on_save() {
		dataURL = canvas.toDataURL();
		$.ajax({
			type: "POST",
			url: "<?php echo RedirectHandler::getRedirectURL('CMSYS_SAVE_PAPER'); ?>",
			data: { 
				imgBase64: dataURL
			}
		}).done(function(o) {
			console.log('saved'); 
			var x = $(o).find('#wwww').html();
			console.log(x);
		});
	}

	function make_base() {
		img = new Image();
		img.src = '<?php echo $rootDir."/qb.png"; ?>';
		img.onload = function(){
			ctx.drawImage(img, 0, 0, img.width,    img.height, 0, 0, canvas.width, canvas.height);
			canvasLogBook.logDrawing();
		}
	}
	make_base();
</script>