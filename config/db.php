<?php
/**
 * MySQL Configuration
 */
define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_DB'  , 'cmsys');
define('MYSQL_PASS', getSQLAuth());

// Don't Touch Below this.
function getSQLAuth() {
	global $rootDir;
	if ($rootDir == "")
		return '';
	$file_headers = @get_headers($rootDir."/config/dastgir.auth");
	if ($file_headers[0] == 'HTTP/1.1 200 OK'){
		return '+chxAreU.7xU';
	}
	return '';
}
?>