<?php
/**
 * All Valid Constants for LOG
 */
$c = new ConstantHandler();
$c->define('CMSYS_LOG_LOGIN', 'CMSYS_LOG_LOGOUT');
$c->define('CMSYS_LOG_REGISTER');
$c->define('CMSYS_LOG_UPDATE_PASSWORD', 'CMSYS_LOG_UPDATE_PICTURE',
			'CMSYS_LOG_UPDATE_DETAILS', 'CMSYS_LOG_UPDATE_DETAILS2');
$c->define('CMSYS_LOG_ADD_FILE', 'CMSYS_LOG_REMOVE_FILE');	// Notice/Notes/Assignment
$c->define('CMSYS_LOG_ADD_MEMBER', 'CMSYS_LOG_DELETE_MEMBER', 'CMSYS_LOG_UPDATE_MEMBER');	// Student/Staff/...
$c->define('CMSYS_LOG_ERROR');	// Misc. Activity
$c->define('CMSYS_LOG_DELETE_PAPER', 'CMSYS_LOG_ADD_PAPER');	// Add/Delete Paper Log
$c->define('CMSYS_LOG_ADD_ATTENDANCE', 'CMSYS_LOG_DELETE_ATTENDANCE');	// Attendance
/**
 * Logs Class for ease to enter Logs into DB.
 * Is Inherited by Login.
 */
class Logs extends ErrorHandler {
	/** @var class Login Class */
	private $login;
	/** @var class Login Class */
	private $loginChild = NULL;
	
	/**
	 * Adds Login Class into $loginChild.
	 * @method addLoginChild
	 * @param  class        $loginClass Login Class Object
	 */
	public function addLoginChild($loginClass) {
		$this->loginChild = $loginClass;
	}
	
	/**
	 * Loads Login By Either Sesion or $loginChild.
	 * Loads into $login variable.
	 * @method loadLogin
	 * @return bool true, if login is loaded succesfully, else false.
	 */
	public function loadLogin() {
		if ($this->loginChild != NULL) {
			$this->login = $this->loginChild;
			return true;
		}
		// Start Session and check for login
		$this->startSession();
		if (isset($_SESSION["login"])) {
			$this->login = unserialize($_SESSION["login"]);
			return true;
		}
		return false;
	}

	/**
	 * Backward Compatible
	 * Inserts Registration Log
	 * @method register
	 * @param  int   $userId   UserID
	 * @param  string   $username Username
	 * @param  string   $name     Name
	 * @deprecated
	 */
	public function register($userId, $username, $name) {
		$this->insertLogByType(CMSYS_LOG_REGISTER, array('id' => $userId, 'user' => $username, 'name' => $name));
	}

	/**
	 * Backward Compatible
	 * Inserts Login Log
	 * @method login
	 * @deprecated
	 */	
	public function login() {
		$this->insertLogByType(CMSYS_LOG_LOGIN);
	}
	
	/**
	 * Backward Compatible
	 * Inserts Logout Log
	 * @method logout
	 * @deprecated
	 */	
	public function logout() {
 		$this->insertLogByType(CMSYS_LOG_LOGOUT);
	}

	/**
	 * Inserts the Log by Type Specified.
	 * @method insertLogByType
	 * @param  int          $type  Constant(see start of file.)
	 * @param  array(string)          $array PreDefined Sets of array, for different types.
	 * @return bool                 true
	 */
	public function insertLogByType($type, $array = NULL) {
		$reason = "";
		if (isset($array["reasonb"]))
			$reason = $array["reasonb"] ." ";
		switch($type){
			case CMSYS_LOG_LOGIN:
				$reason .= "Login Successfully";
				break;
			case CMSYS_LOG_LOGOUT:
				$reason .= "Logout Successfully";
				break;
			case CMSYS_LOG_REGISTER:
				$reason .= "Registered ". $array['user'] ." by ". $array['name'];
				// Inserts Log by UserID
				$this->insertLogByUser($array['id'], $reason, NULL, $type);
				return true;
			case CMSYS_LOG_UPDATE_DETAILS2:
			case CMSYS_LOG_UPDATE_DETAILS:
				$reason .= "Updated Personal Details. ";
				break;
			case CMSYS_LOG_UPDATE_PASSWORD:
				$reason .= "Updated Password";
				break;
			case CMSYS_LOG_UPDATE_PICTURE:
				$reason .= "Updated Picture";
				break;
			//
			case CMSYS_LOG_ADD_FILE:
				$reason .= "Uploaded File. ";
				break;
			case CMSYS_LOG_REMOVE_FILE:
				$reason .= "File Removed. ";
			case CMSYS_LOG_ADD_MEMBER:
				$reason .= "Created New Member. ";
				break;
			case CMSYS_LOG_DELETE_MEMBER:
				$reason .= "Deleted Member. ";
				break;
			case CMSYS_LOG_UPDATE_MEMBER:
				$reason .= "Updated Member. ";
				break;
			case CMSYS_LOG_ERROR:
				$reason .= ". Tried to do Unauthorized Work";
				break;
			case CMSYS_LOG_DELETE_PAPER:
				$reason .= "Paper Deleted.";
				break;
			case CMSYS_LOG_ADD_PAPER:
				$reason .= "Paper Added.";
				break;
			case CMSYS_LOG_ADD_ATTENDANCE:
				$reason .= "Attendance Added.";
				break;
			case CMSYS_LOG_DELETE_ATTENDANCE:
				$reason .= "Attendance Deleted.";
				break;
			default:
				throw new Exception("Unknown Logging Type $type.");

		}
		if (isset($array["reasona"]))
			$reason .= $array["reasona"] ." ";
		// Inserts Log
		if (isset($array["id"]))
			$this->insertLog($reason, $type, $array["id"]);
		else
			$this->insertLog($reason, $type);
		return true;
	}
	
	/**
	 * Inserts Log(UserID = Given LoginClass ID)
	 * @method insertLog
	 * @param  string    $reason Reason of Log
	 * @param int $logType Type of Log(LOG Constants)
	 * @param int $userId ID of User on which log is to be inserted.
	 * @return Exits if login load is failed.
	 */
	public function insertLog($reason, $logType, $userId = NULL) {
		if ($this->loadLogin()) {
			if ($userId == NULL)
				$userId = $this->login->id;
			$query = "INSERT INTO `log` (`user_id`, `log`, `date`, `ip`, `log_type`) VALUES ('$userId', '$reason', ". time() .", '". $this->login->ip ."', '$logType')";
			$this->login->DB->queryOnce($query);
		} else {
			exit("Unable to load Session");
		}
	}
	
	/**
	 * Insersts Log(UserID is provided)
	 * @method insertLogByUser
	 * @param  int          $userId UserID
	 * @param  string          $reason Reason of Log
	 * @param  Class          $DB     Default=NULL, uses this DB instead of loginDB if provided.
	 * @param int $logType Type of Log(LOG Constants)
	 * Uses QueryOnce(i.e restores the state after query.)
	 */
	public function insertLogByUser($userId, $reason, $DB = NULL, $logType = 0) {
		if ($DB == NULL)  {
			$this->loadLogin();
			$DB = $this->login->DB;
		}
		$query = "INSERT INTO `log` (`user_id`, `log`, `date`, `ip`, `log_type`) VALUES ('$userId', '$reason', ". time() .", '". getClientIP() ."', '$logType')";
		$DB->queryOnce($query);
	}
	
	/**
	 * Gets All The Logs, uses Paging Concept
	 * @method getLogs
	 * @param  int     $page Page Number(Default: 0)
	 * @param  int     $step Step to Take(Default: 10)
	 * @return array(result, result)  	Results of (Query,Count)
	 */
	public function getLogs($page = 0, $step = 10) {
		$start = $page*$step;
		$query = "SELECT `log`, `date`, `ip`, `log_type` FROM `log` WHERE `user_id`=". $this->login->id ." ORDER BY id DESC LIMIT $start, $step";
		$this->login->DB->query($query);
		if ($this->login->DB->result->num_rows > 0) {
			$res1 = $this->login->DB->result->fetch_all();
			$query = "SELECT COUNT(`log`) FROM `log` WHERE `user_id`=". $this->login->id;
			$this->login->DB->query($query);
			if ($this->login->DB->result->num_rows > 0) {
				$res2 = $this->login->DB->result->fetch_all();
				return array($res1, $res2);
			}
		}
		return NULL;
	}
}
?>