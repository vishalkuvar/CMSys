<?php
// Required Modules and Configs
require_once(dirname(__FILE__).'/../include.php');

/**
 * ErrorHandler Class
 * This Class contains methods for processing Sessions, Redirects,
 * Error Messages and Info Messages.
 * @see modules/errorHandler.php
 **/
 
class ErrorHandler {
	public static $error = array();		// Array that will be converted to json, via getError method.
	public static $info = array();		// Array that will be converted to json, via getInfo method.
	private $defaultRedirect = NULL;	// Default Redirect if there's no redirect specified.
	
	/**
	 * Static Function, Clears the Error
	 * @method cError
	 */
	public static function cError() {
		ErrorHandler::$error = array();
	}
	
	/**
	 * Clears the Error
	 * @method clearError
	 * @see ErrorHandler::cError
	 */
	public function clearError() {
		ErrorHandler::$error = array();
	}
	
	/**
	 * Displays the Error in the HTML and clears all previous Error.
	 * @method showError
	 * @param  int       $type Type: 0 = Normal, 1 = Transparent
	 * @return int          number of errors displayed.
	 */
	public static function showError($type = 0) {
		$e = new ErrorHandler();
		$e->startSession();
		$count = 0;
		// Select Box Transparency.
		switch($type) {
			case 1:
				$class = 'cmsys-error-trans';
				break;
			default:
				$class = 'cmsys-error';
				break;
		}
		// If Session not empty, show the errors.
		if (!empty($_SESSION['error'])) {
			echo '<div class="'. $class .'">';
			$errors = json_decode($_SESSION['error']);
			if (is_array($errors)) {
				foreach($errors as $error) {
					$count++;
					echo $error .'<br/>';
				}
			} else {
				$count++;
				echo $errors .'<br/>';
			}
			echo '</div>';
			echo '<br/>';
			$_SESSION['error'] = NULL;
			ErrorHandler::$error = array();
		}
		// Return Count.
		return $count;
	}
	
	/**
	 * Displays the Info in the HTML and clears all previous Info
	 * @method showInfo
	 * @param  int       $type Type: 0 = Normal, 1 = Transparent
	 * @return int   number of info displayed.
	 */
	public static function showInfo($type = 0) {
		$e = new ErrorHandler();
		$e->startSession();
		$count = 0;
		// Select Box Transparency.
		switch($type) {
			case 1:
				$class = 'cmsys-info-trans';
				break;
			default:
				$class = 'cmsys-info';
				break;
		}
		// If Info is not empty, display them.
		if (!empty($_SESSION['info'])) {
			echo '<div class="'. $class .'">';
			$infos = json_decode($_SESSION['info']);
			if (is_array($infos)) {
				foreach($infos as $info) {
					echo $info .'<br/>';
					$count++;
				}
			} else {
				echo $infos .'<br/>';
				$count++;
			}
			echo '</div>';
			echo '<br/>';
			$_SESSION['info'] = NULL;
			ErrorHandler::$info = array();
		}
		// Return the count.
		return $count;
	}
	
	/**
	 * Starts the Ssssion if not yet started.
	 * @method startSession
	 */
	public function startSession() {
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	}
	
	/**
	 * Add's Default Redirect if no redirect is found.
	 * @method addRedirect
	 * @param  string      $link Constant, @see RedirectHandler
	 */
	public function addRedirect($link) {
		$this->defaultRedirect = $link;
	}
	
	/**
	 * Redirects if any error is found
	 * @method errorRedirect
	 * @param  string        $file Constant, @see RedirectHandler(optional)
	 * @param string $additionalRedirect Adds More Link to the URL(usually for identifying userID)
	 * @return bool Returns true/false if no redirect link is provided.
	 */
	public function errorRedirect($file = NULL, $additionalRedirect = NULL) {
		$isError = !empty(ErrorHandler::$error);

		// If no redirect path is defined, just return true/false	
		if ($file == NULL && $this->defaultRedirect == NULL) {
			return $isError;
		}
		
		// If Error, Redirect.
		if ($isError) {
			if ($file != NULL)
				$this->redirect($file, $additionalRedirect);
			$this->redirect($this->defaultRedirect, $additionalRedirect);
		}
	}
	
	/**
	 * Redirect with Additional Session of error and info
	 * @method redirect
	 * @param  string   $fileName File Constant, @see RedirectHandler
	 * @param string $additionalRedirect Adds More Link to the URL(usually for identifying userID)
	 */
	public function redirect($fileName, $additionalRedirect = NULL) {
		/** If Error, Add JsonEncoded Error into Session, */
		if ($this->isError()) {
			$this->startSession();
			$_SESSION['error'] = $this->getError();
		}
		/** If Info present, Add JsonEncoded Info into Session, */
		if ($this->isInfo()) {
			$this->startSession();
			$_SESSION['info'] = $this->getInfo();
		}
		// If $fileName is constant, redirect via RedirectHandler.
		if (defined($fileName)) {
			RedirectHandler::redirect($fileName, $additionalRedirect);
		} else
			redirect($fileName, $additionalRedirect);
	}
	
	/**
	 * If $file is null, returns true/false, 
	 * else Redirects to the $file link provided
	 * @method isError
	 * @param  string(optional)  $file File Constant, @see RedirectHandler
	 * @return bool          True if error found.
	 */
	public function isError($file = null) {
		$isError = !empty(ErrorHandler::$error);
		if ($file == null)
			return $isError;
		if ($isError)
			$this->redirect($file);
	}
	
	/**
	 * Returns error in json Format
	 * @method getError
	 * @return string(json)  Error converted into json format.
	 */
	public function getError() {
		return json_encode(ErrorHandler::$error);
	}
	
	/**
	 * Add's Error into ErrorHandler
	 * @method addError
	 * @param  string   $errorMsg Error Message
	 */
	public function addError($errorMsg) {
		ErrorHandler::$error[] = $errorMsg;
	}
	
	/**
	 * Checks if any Info is present.
	 * @method isInfo
	 * @return bool   True, if info is present.
	 */
	public function isInfo() {
		return (!empty(ErrorHandler::$info));
	}
	
	/**
	 * Converts Info into json format.
	 * @method getInfo
	 * @return string(json)  Info.
	 */
	public function getInfo() {
		return json_encode(ErrorHandler::$info);
	}
	
	/**
	 * Add's Info into @see ErrorHandler::$info
	 * @method addInfo
	 * @param  string  $infoMsg Information Message
	 */
	public function addInfo($infoMsg) {
		ErrorHandler::$info[] = $infoMsg;
	}
}
?>