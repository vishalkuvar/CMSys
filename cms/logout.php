<?php
/**
 * Logout Page
 * Destroys the Session
 */
require_once(dirname(__FILE__).'/../include.php');
// Start Session.
session_start();
// If Session Exist, call logout function.
if (isset($_SESSION["login"])) {
	$login = unserialize($_SESSION['login']);
	$login->logs->logout();
}
if (session_destroy()) {	// Destroying All Sessions
	RedirectHandler::redirect('CMSYS_INDEX');
} else {
	error("Logout Failed.");
}
?>
