<?php
/**
 * Registration Page
 */
require_once(dirname(__FILE__).'/../include.php');
// Start Session
session_start();
require_once(dirname(__FILE__).'/session.php');
if ($sessionError == false && $isLoginSet == true) {	// Already Authenticated, Redirect to Profile.
	RedirectHandler::redirect('CMSYS_PROFILE');
}
// Initialize Classes
$login = new Login('CMSYS_REGISTER');
// Check if Valid Post Requests are sent.
$login->check();
$login->errorRedirect();	// Redirect if Error
// Check Required Fields from POST request and copy it off to $login Class
$login->checkEmpty(array('username'), "No Username Provided");
$login->checkEmpty(array('password', 'password_confirmed', 'email', 'name'), "Some Fields are not provided");
$login->errorRedirect();
// Check if Both Password Matches.
if ($login->G['password'] != $login->G['password_confirmed']) {
	$login->addError("Passwords do not match.");
	$login->errorRedirect();
}
// Check if Email is entered in correct format.
if (!$login->isValidEmail($login->G['email'])) {
	$login->addError("Invalid Email Provided.");
	$login->errorRedirect();
}

// Store in temporary vairables.
$username = $login->G['username'];
$password = $login->G['password'];
$email = $login->G['email'];
$name = $login->G['name'];
// Escape Strings to protect MySQL Injection
$login->DB->escape($username);
$login->DB->escape($password);
$login->DB->escape($email);
$login->DB->escape($name);
// Check if User Already Exists.
$rows = $login->checkUser($username);
if (count($rows) > 0) {
	$login->DB->close();
	$login->addError("Username Already Exists, Please Choose another Username");
	$login->errorRedirect();
}
// Insert the User
$login->register($username, $password, $email, $name, false);
$login->addInfo("Successfully Registered, Please Verify Your Email");
$login->DB->close();
// Redirect
$login->redirect('CMSYS_INDEX');
?>
