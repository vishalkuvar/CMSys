<?php
// URL where the CMSys is hosted(wihtout directory)
$url = "https://dastgir.tech/";
// Directory where CMSYS is
$dir = "cmsys";
// Expiration Time for Verification Email (Default: 1 Day)
$codeExpire = 24*60*60;
// RootDir: URL.DIR (Don't Edit Below This)
$rootDir = $url . $dir;

/**
 * Autoload Module if not already loaded.
 * Checks for module first, then check for trait.
 * @method __autoload
 * @param  string     $classname ClassName
 */
function __autoload($classname) {
	if (file_exists(dirname(__FILE__).'/modules/'. $classname .'.php'))
		$filename = dirname(__FILE__).'/modules/'. $classname .'.php';
	else
		$filename = dirname(__FILE__).'/modules/traits/'. $classname .'.php';
    include_once($filename);
}

/**
 * Returns the ClientIP
 * @method getClientIP
 * @return string      IP Address(IPv4/IPv6)
 */
function getClientIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
 
    return $ipaddress;
}

/**
 * Redirects the User to specified Host:URL with additionalURL(optional)
 * @method redirect
 * @param  string   $file       Host with URL
 * @param  string   $additional (default: NULL), Additional URL
 */
function redirect($file, $additional = NULL) {
	global $rootDir;
	if (basename($_SERVER["PHP_SELF"]) == $file)
		return;
	$file = RedirectHandler::getAdditionalURL($file, $additional);
	
	header('Location: '. $rootDir .'/'. $file);
	exit();
}

/**
 * Checks if Server supports HTTPS or now.
 * @method sucureWeb
 * @return string    protocol(http/https)
 */
function sucureWeb() {
	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
		return "http://";
	}
	return "https://";
}

$ip = getClientIP();
// If IP is localhost, adjust the rootDir
if ($ip == "::1" || $ip == "127.0.0.1") {
	$url = $_SERVER["HTTP_HOST"];	// Auto Adjust Port
	$rootDir = "http://$url/". $dir;
}

?>