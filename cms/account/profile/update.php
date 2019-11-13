<?php
	/**
	 * Update Profile Page
	 */
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/input2.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/jquery-ui.css">';
	echo '<script src="'. $rootDir .'/js/jquery-ui.js" type="text/javascript"></script>';
/**
 * Start Form Div Tag
 * @method startDiv
 */
function startDiv() {
	echo '<div class="cmsys-form">';
}
/**
 * End Div Tag
 * @method endDiv
 */
function endDiv() {
	echo '</div>';
}
/**
 * Start Form Tag with Parameters(Always POST).
 * @method startForm
 * @param  string    $action URL to redirect to.
 * @param  string    $class  Class(for css)
 */
function startForm($action, $class = NULL, $onSubmit = NULL) {
	$prep = "<form ";
	if ($class != NULL) {
		$prep .= "class='$class' ";
	}
	if ($onSubmit != NULL) {
		$prep .= "onsubmit='$onSubmit' ";
	}
	$prep .= "action='$action' method='POST'>";
	echo $prep;
}
/**
 * Close the Form Tag.
 * @method endForm
 */
function endForm() {
	echo '</form>';
}
/**
 * Start Heading(Heading inside form is always centered and dark blue color)
 * @method heading
 * @param  string  $heading Text in Heading
 */
function heading($heading) {
	echo "<h3>$heading</h3>";
}

/**
 * Start Input Box with extraTags.
 * extraTags are the tags mentioned inside <input> tag.
 * @method input
 * @param  array $extraTags format: 'tag' => 'value', multi-valued
 * @param  string $text      Text to Show before input
 * @param float $multipler MinWidth Multiplier(Default: 1)
 */
function input($extraTags = NULL, $text = NULL, $multiplier = 1) {
	global $login;
	$prep = "";
	if ($text != NULL)
		$prep .= "<span><div style='min-width: ". (80*$multiplier) ."px; float: left; word-warp: break-word; display: inline-block;'>".$text .": </div></span>";
	if ($extraTags['type'] == "textarea") {
		$prep .= "<textarea ";
	} else {
		$prep .= "<input ";
	}
	$defValue = '';
	if ($extraTags != NULL) {
		if (isset($extraTags['name'])) {
			$nameTag = $extraTags['name'];
			switch($nameTag) {
			case 'password':
			case 'email':
				break;
			default:
				if (isset($login->$nameTag)) {
					$defValue = $login->$nameTag;
					$extraTags['value'] = $defValue;
				}
				break;
			}
		}
		foreach($extraTags as $tag => $value) {
			if ($value == "textarea")
				continue;
			$prep .= "$tag='$value' ";
		}
	}
	$prep .= ">";
	if ($extraTags['type'] == "textarea")
		$prep .= $defValue. "</textarea>";
	echo $prep;
}


/**
 * Give a <br> tag.
 * @method br
/ */
function br() {
	echo "<br/>";
}

/**
 * Start/End center
 * @method center
 * @param  bool   $end if true, will close the center tag.
 */
function center($end = false) {
	if ($end == true) {
		echo "</center>";
	} else
		echo "<center>";
}
?>
<!-- Update Profile Template -->
<div style="margin-right:10px; display:inline-block">
	<a href="#" class="thumbnail" style="width:242px; height: 200px; border:0; box-shadow:0; border-radius:0;">
		<form action='<?php echo RedirectHandler::getRedirectURL('CMSYS_SYSTEM_UPDATE_PICTURE'); ?>' id='image_form' method='POST' style='width: 10%' enctype="multipart/form-data">
		<input type="file" id="profile-image-upload" name="profile-image-upload" style="position: absolute; left: -9999px;" type="file" onclick="onProfileUploadClicked()" onchange="onProfileUploaded()">
			<div style="cursor: pointer; width:242px; height: 200px;  display:inline-block;" onclick="onProfileClick()">
				<img src="<?php
						$fileHandler = new FileHandler(UPLOAD_PICTURE);
						if (($image = $fileHandler->get($login->id)) != NULL) {
							$base64 = 'data:image/png;base64,' . base64_encode($image);
							echo $base64;
						} else {
							echo $rootDir."/bg/242x200.svg";
						}
						?>" style="width:242px; height: 200px;" />
			</div>
		<input type="submit" id="profile-image-upload-next" value="submit" style="position: absolute; left: -9999px;">
		</form>
	</a>
</div>
<div style="display:inline-block; width: 75%;  float: right;" class="cmsys-info1">
	<h1>Update Details:</h1>
	<p>You can update your Profile details here</p>
	<small>Please fill correct details as this will be used for contacting/emergency cases.</small><br>
</div>
<div style="margin-top: 50px;">
<?php
	ErrorHandler::showError();
	ErrorHandler::showInfo();
?>
</div>
<?php
// Creating form via PHP so as to reduce chances of mistake in HTML.
// Also decreases the ugliness of code.
// // Input Class
$inputClass = "cmsys-fill cmsys-radio2";
// Input Class
$inputClass = "cmsys-fill cmsys-input2";
// Button Class
$buttonClass = "cmsys-fill cmsys-button2";
$gap = 4;
// Outer Box
center();
startDiv();
	//
	startForm(RedirectHandler::getRedirectURL('CMSYS_SYSTEM_UPDATE_PROFILE'), NULL, "return validateDetails();");
		// Local Address
		startDiv();
			heading("Local Address");
			echo "<hr>";
			input(array("required" => "true", "type" => "textarea", "id"=>"a1", "name" => "address", "rows"=> "3", "class" => $inputClass, "placeholder" => ''), "Address"); br();
			input(array("required" => "true", "type" => "text", "id"=>"a2", "name" => "pincode", "class" => $inputClass, "placeholder" => ''), "</br>Pincode"); br();
			input(array("required" => "true", "type" => "text", "id"=>"a3", "name" => "mobile_no", "class" => $inputClass, "placeholder" => ''), "</br>Mobile no."); br();
		endDiv();
		echo '<i class="fa fa-arrow-right fa-3x" id="transfer_locAdd_resAdd" style="position: relative; bottom: 80px;" aria-hidden="true"></i>';
		// Permanent Address
		startDiv();
			heading("Permanent Address");
			echo "<hr>";
			input(array("required" => "true", "type" => "textarea", "id"=>"b1", "name" => "address2", "rows"=> "3", "class" => $inputClass, "placeholder" => ''), "Address"); br();
			input(array("required" => "true", "type" => "text", "id"=>"b2", "name" => "pincode2", "class" => $inputClass, "placeholder" => ''), "</br>Pincode"); br();	
			input(array("required" => "true", "type" => "text", "id"=>"b3", "name" => "mobile_no2", "class" => $inputClass, "placeholder" => ''), "</br>Mobile no."); br();
		endDiv();
		// New Line
		br();
		startDiv();
			heading("Additional Details");
			echo "<hr>";
			input(array("type" => "radio", "name" => "sex", "id" => "radio-male", "class" => "hide-radio", "value" => "Male", "checked" => "true"), "Sex", $gap);
			echo '<label for="radio-male">Male</label>';
			input(array("type" => "radio", "name" => "sex", "id" => "radio-female", "class" => "hide-radio", "value" => "Female"));
			echo '<label for="radio-female">Female</label>';
			br();
			br();
			input(array("required" => "true", "type" => "text", "name" => "date", "id" => "datepicker", "class" => $inputClass, "placeholder" => "31-01-2016"), "</br>Date Of Birth", $gap);
			input(array("type" => "text", "name" => "date_words", "id" => "datepicker_w", "class" => $inputClass, "readonly" => "true", "placeholder" => "Date(In Words): Auto Generated")); br();
			input(array("required" => "true", "type" => "text", "name" => "birth_place", "class" => $inputClass), "</br>Place of Birth", $gap); br();
			input(array("required" => "true", "type" => "text", "name" => "religion", "class" => $inputClass), "</br>Religion", $gap); br();
			input(array("type" => "text", "name" => "caste", "class" => $inputClass), "</br>Caste", $gap); br();
			input(array("required" => "true", "type" => "text", "name" => "category", "class" => $inputClass, "placeholder" => "Open/SC/ST/..."), "</br>Category", $gap); br();
			input(array("type" => "text", "name" => "blood_group", "class" => $inputClass), "</br>Blood Group", $gap); br();
		endDiv();
		br();
		startDiv();
			input(array("type" => "submit", "class" => $buttonClass, "value" => "Update  Details"));
		endDiv();
	endForm();
	br();
	echo "<hr>";
	// Login Details(Inner Box)
	startDiv();
		// Form and it's Details.
		startForm(RedirectHandler::getRedirectURL('CMSYS_SYSTEM_UPDATE_DETAILS'), "login-form");
			heading("Update Login Details");
			echo "<hr>";
			input(array("type" => "text", "name" => "name", "class" => $inputClass, "placeholder" => 'Name: '. $login->name)); br();
			input(array("type" => "text", "name" => "username", "class" => $inputClass, "placeholder" =>'Username: '. $login->user)); br();
			input(array("type" => "text", "name" => "email", "class" => $inputClass, "placeholder" =>'Email: '. $login->email)); br();
			center();
			br();
				input(array("type" => "submit", "class" => $buttonClass, "value" => "Update Details"));
			center(true);
		endForm();
	endDiv();
	// Password ()
	startDiv();
		startForm(RedirectHandler::getRedirectURL('CMSYS_SYSTEM_UPDATE_PASSWORD'));
			heading("Update Password");
			echo "<hr>";
			input(array("type" => "password", "name" => "old_password", "class" => $inputClass, "placeholder" =>'Current Password')); br();
			input(array("type" => "password", "name" => "password", "class" => $inputClass, "placeholder" =>'New Password')); br();
			input(array("type" => "password", "name" => "password_confirmation", "class" => $inputClass, "placeholder" =>'Verify Password')); br();
			center();
			br();
				input(array("type" => "submit", "class" => $buttonClass, "value" => "Change Password"));
			center(true);
		endForm();
	endDiv();
	br();	// Go to Next Line

	// Update User Details
	/*
	startDiv();
		startForm("");
			heading("User Details");
			input(array("type" => "text", "class" => $inputClass, "placeholder" => $login->name, "style" => "margin-right:15px;"), "First Name");
			input(array("type" => "text", "class" => $inputClass, "placeholder" => $login->name), "Last Name");
		endForm();
	endDiv();
	*/
endDiv();
center(true);
?>

<script type="text/javascript">
	function onProfileUploadClicked() {
		//document.getElementById("profile-image-upload-next").click();
	}
	function onProfileClick() {
		 document.getElementById("profile-image-upload").click();
	}
	function onProfileUploaded() {
		document.getElementById("image_form").submit();
	}
	var arrow = document.getElementById("transfer_locAdd_resAdd");
	// Copy Local Address to Permanent Address.
	arrow.onclick = function() {
		for (var i = 1; i <= 3; i++) {
			document.getElementById("b"+ i).value = document.getElementById("a"+ i).value;
		}
	}
	// Initialize Date
	$( function() {
		$("#datepicker").datepicker();
		$("#datepicker").datepicker("option", "showAnim", "slideDown");
		$("#datepicker").datepicker("option", "dateFormat", "dd-mm-yy");
		$("#datepicker").datepicker("option", "altField", "#datepicker_w");
		$("#datepicker").datepicker("option", "altFormat", "DD, dd MM yy");
		<?php
			if (isset($login->date)) {
				echo '$("#datepicker").datepicker("setDate", "'. $login->date .'");';
			}
		?>
	});

	function validatePhone(elementName) {
		var inputPhone = document.getElementsByName(elementName);
		if ((inputPhone[0].value.match(/^\d{10}$/))) {
			return true;
		}
		alert("Invalid Phone Number");  
		return false;
	}

	function validateDetails() {
		var validated = false;
		// Phone Number Validation
		validated = validatePhone("mobile_no");
		if (validated == false) return false;
		validated = validatePhone("mobile_no2");
		if (validated == false) return false;
		return true;
	}
</script>