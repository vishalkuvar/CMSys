<?php 
$empty = 0;
$data = array();
$column = array();
/**
 * Checks for Post Argument,
 * inserts data into global $data and $column if POST is not empty.
 * @method emptyCheck
 * @param  string     $var         POST Argument
 * @param  int     $addEmpty    value to be added in $empty
 * @param  bool     $required  (default: true), Is this field required?
 * @return string                  Contents of POST Argument.
 */
function emptyCheck($var, $addEmpty, $required = true) {
	global $empty, $data, $column;
	global $login, $log;
	$temp = '';
	if (!empty($_POST[$var])) {
		$temp = $_POST[$var];
		if ($var == 'sex') {
			$temp = substr($temp, 0, 1);
		}
		$data[] = $temp;
		$column[] = $var;
		$empty |= $addEmpty;
		$log .= "$var: $temp, ";
		$login->DB->escape($temp);
	} else if ($required) {
		$login->addError("Some Fields are Missing($var).");
		$login->errorRedirect("CMSYS_UPDATE_PROFILE");
	}
	return $temp;
}

/**
 * Validates the Given date and format.
 * @method validateDate
 * @param  string       $date   Date
 * @param  string       $format Date Format
 * @return bool               true if date is valid, else false.
 */
function validateDate($date, $format = 'd-m-Y') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

$log = '';
// Insert Data for UserID
$data[] = $login->id;
$column[] = 'userId';
// $_POST to variables.
$address	=		emptyCheck('address',		0x0001);
$pincode	=		emptyCheck('pincode',		0x0002);
$mobNo		=		emptyCheck('mobile_no',		0x0004);
$address2	=		emptyCheck('address2',		0x0008);
$pincode2	=		emptyCheck('pincode2',		0x0010);
$mobNo2		=		emptyCheck('mobile_no2',	0x0020);
$sex		=		emptyCheck('sex',			0x0040);
$date		=		emptyCheck('date',			0x0080);
//$date_words	=		emptyCheck('date_words',	0x0100, false);
$birth_place=		emptyCheck('birth_place',	0x0200);
$religion	=		emptyCheck('religion',		0x0400);
$caste		=		emptyCheck('caste',			0x0800, false);
$category	=		emptyCheck('category',		0x1000);
$blood_group=		emptyCheck('blood_group',	0x2000, false);

// Validation
if (!preg_match('/^\d{10}$/', $mobNo)) {
	$login->addError("Invalid Mobile Number(Local Address).");
} else if (!preg_match('/^\d{10}$/', $mobNo2)) {
	$login->addError("Invalid Mobile Number(Permanent Address).");
} else if (!intval($pincode) || !intval($pincode2)) {
	$login->addError("Invalid Pin Code Entered.");
} else if (strtoupper($sex) != "M" && strtoupper($sex) != "F") {
	$login->addError("Invalid Sex.");
} else if (!validateDate($date)) {
	$login->addError("Invalid Date Format.");
}
$login->errorRedirect("CMSYS_UPDATE_PROFILE");
// Log
$login->logs->insertLogByType(CMSYS_LOG_UPDATE_DETAILS2, array("reasona" => $log));
// Add Into Table
$login->replaceTable('user_details', $column, $data);
// Redirect
$login->addInfo("Details Updated");
$login->redirect("CMSYS_UPDATE_PROFILE");
?>