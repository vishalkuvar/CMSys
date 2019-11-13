<?php
// Verification Code Class
/*
	VERIFYCODE_NEWACCOUNT = 0
	VERIFYCODE_RESETPASSWOR = 1
*/
// Required Modules/config
require_once(dirname(__FILE__).'/../../../include.php');
// Check if URL contains id and code
if (isset($_GET["id"]) && isset($_GET["code"])) {
	// Initiate DB and Logs Class
	$error = new ErrorHandler('CMSYS_INDEX');
	$DB = new DB();
	$log = new Logs();
	// Unable to Contact DB
	if ($DB->isError()) {
		$error->addError("Unable to Contact Web Server, Please Try again Later.");
		$error->redirect('CMSYS_INDEX');
	}
	// Store id and code in Variables
	$id = $_GET["id"];
	$code = $_GET["code"];
	// Escape them, since it's provided by user, it can be dangerous too.
	$DB->escape($id);
	$DB->escape($code);
	// Execute
	$DB->query("SELECT * FROM verification_code WHERE user_id='$id' AND `code`='$code'");
	// No Result Found, means wrong verification_code/UserID
	if ($DB->result->num_rows == 0) {
		$error->addError("Invalid ID/Code Combination.");
		$error->redirect('CMSYS_INDEX');
	}
	$verify = $DB->result->fetch_assoc();
	// Get Current Time and Expiration Time of verification code
	$time = time();
	$start = intval($verify['creation_date']);
	$end = intval($verify['expiration_date']);
	if ($time < $start || $time > $end) {
		$error->addError("Verification Code Expired");
		$error->redirect('CMSYS_INDEX');
		// Resend Verification Code WIP.
	}
	if (intval($verify['type']) != 0) {
		$error->addError("Invalid Verification Code.");
		$error->redirect('CMSYS_INDEX');
	}
	// WIP: Check Already Verified user.
	$DB->query("SELECT `verified` FROM `login` WHERE id='$id'");
	if ($DB->isError()) {
		$error->addError("Cannot Connect to WebServer, Please try again.");
		$error->redirect('CMSYS_INDEX');
	}
	if ($DB->result->num_rows == 0) {
		$error->addError("There's some Problem while verifying your account, Please consider creating new account or contact support.");
		$error->redirect('CMSYS_INDEX');
	}
	$verify = $DB->result->fetch_assoc();
	if (intval($verify['verified']) == 1) {
		$error->addInfo('User is Already Verified');
		$error->redirect('CMSYS_INDEX');
	}
	$DB->query("UPDATE `login` SET `verified`='1' WHERE id='$id'");
	if ($DB->isError()) {
		$error->addError("Error Verifying the User");
		$error->redirect('CMSYS_INDEX');
	}
	$log->insertLogByUser($id, "Email Verified", $DB);
	$error->addInfo("User Verified Successfully.");
	$error->redirect('CMSYS_INDEX');
}
?>