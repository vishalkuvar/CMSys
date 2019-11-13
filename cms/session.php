<?php
/**
 * Session Verification Page
 */

require_once(dirname(__FILE__).'/../include.php');
function sessionVerify() {
	global $login;
	$e = new ErrorHandler();
	$error = false;	// Set error to false.
	$e->startSession();
	// Check if Login Exists
	if (isset($_SESSION['login'])) {
		// Generate class from Session.
		$login = unserialize($_SESSION["login"]);
		if (isset($login->user) && isset($login->decPass)) {	// If User Exists, verify it.
			$login->checkUser($login->user, 1, $login->decPass);
			// If User is unverified, show error.
			if (!isset($login->verified) || $login->verified == 0) {
				$e->addError("Please Verify Account to Login.");
			}
		} else if (!isset($login)) {
			$e->addError("Invalid Username or Password...");
		} else {
			$error = true;
		}
	}

	if (!$error)
		$error = $e->isError();

	return $error;
}

function checkLogin() {
	if (isset($_SESSION["login"])) {
		// Generate class from Session.
		$login = unserialize($_SESSION["login"]);
		if (isset($login->user) && isset($login->decPass)) {	// If User Exists, verify it.
			if (isset($login->verified) && $login->verified == 1) {
				return true;
			}
		}
	}
	return false;
}
$sessionError = sessionVerify();
$isLoginSet = checkLogin();
?>