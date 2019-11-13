<?php
/**
 * Login Verification Page.
 */

require_once(dirname(__FILE__).'/../include.php');
session_start();	// Start Session.
require_once(dirname(__FILE__).'/session.php');
if ($sessionError == false && $isLoginSet == true) {	// If Already Authenticated, redirect to profile.
	redirect(RedirectHandler::redirect('CMSYS_PROFILE'));
}
// Clear All Previous Errors.
ErrorHandler::cError();
$_SESSION["login"] = NULL;
// Initialize Classes
$login = new Login('CMSYS_INDEX');
// Check if Valid Post Requests are sent.
$login->check();
$login->errorRedirect();	// Redirect if Error
// Check Required Fields from POST request and copy it off to $login Class
$login->checkEmpty(array('username', 'password'), "Username or Password is Invalid");
$login->errorRedirect();
// Store Login Username in Session
$_SESSION['login_user'] = $login->G['username'];
$username = $login->G['username'];
$password = $login->G['password'];
// Escape Strings to protect MySQL Injection
$login->DB->escape($username);
$login->DB->escape($password);
// SQL query to fetch information of registerd users and finds user match.
$user = $login->checkUser($username, 3, $password);
// If User found, Check Verifed account.
if ($user != NULL) {
	if (intval($user['verified']) == 0) {
		$login->addError("Please Verify Account before login.");
		$login->errorRedirect();
	}
	// Update Session and Redirect.
	$login->updateSession();
	$login->redirect('CMSYS_PROFILE');
}
$login->addError("Username or Password is invalid");
$login->errorRedirect();
?>
