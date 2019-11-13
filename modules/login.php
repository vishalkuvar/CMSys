<?php
/**
 * This Class will handle all the Authentication Stuff
 * Authentication Stuff includes Login and Registration.
 **/

require_once(dirname(__FILE__).'/../include.php');

class Login extends ErrorHandler {
	public $G = array();		// Array will be replicating all $_POST Contents
	public $DB;					// DB Connection for Login table.
	public $logs;				// logs Class for login table.
	private $decPass;			// Decrypted Password would be stored here.

	public $subjects = array();	// Subjects that a teacher can perform.
	public $semArray = array(); // Eligible Semester(Previous Semesters for Student, All eligble for others)
	
	use DynamicVariables;		// Trait to be used.
	use SubjectHandler;
	
	/**
	 * Checks the HASHED password with the provided decrypted password.
	 * @method checkPassword
	 * @param  string        $password Decrypted Password
	 * @return bool                  True if password matches
	 */
	public function checkPassword($password) {
		if (!password_verify($password, $this->password)) {
			return false;
		}
		return true;
	}
	
	/**
	 * Constructor: Initiates all Classes and redirections.
	 * @method __construct
	 * @param  string      $redirect Redirect Constant
	 */
	public function __construct($redirect) {
		// Initiates Logs
		$this->logs = new Logs();
		// Initiates DB
		$this->DB = new DB();
		$this->DB->addRedirect($redirect);
		$this->DB->errorRedirect();
		// Initiates Redirect
		$this->addRedirect($redirect);
		// Adds Login Child.
		$this->logs->addLoginChild($this);
		// Subjects to Branches
		$this->generateBranches();
		$this->subjectsToBranches();
	}
	
	/**
	 * Checks if $roleName can be performed by the current User.
	 * @method roleValid
	 * @param  string    $roleName roleName
	 * @return bool              true if role can be performed.
	 */
	public function roleValid($roleName) {
		global $is32Bit;
		//var_dump(roleHandler::$listOfRoles);
		// Get full List of Roles a Title can perform.
		// Get Constant name for ROLE.
		$role = 'ROLE_'. strtoupper($roleName);
		$role2 = false;
		// Check if Role is defined, and return exception if not present.
		if (!defined($role)) {
			if ($is32Bit) {	// Extra Processing for 32bit system.
				$role2 = true;
				$role = 'ROLE2_'. strtoupper($roleName);
			}
			if (!defined($role)) {
				throw new Exception("Unknown Role $role in Navigation Bar");
				return false;
			}
		}
		// Convert to constant integer
		$roleId = constant($role);
		// Perform Bitwise And, if value present, return true, else false.
		if (!$role2) {	// 64 Bit System will go here, 32bit system will check here for first 32 roles.
			$allRolesInId = roleHandler::$listOfRoles[$this->title];
			if (($allRolesInId&$roleId) > 0)
				return true;
		} else {	// Higher 32 roles will be checked here.
			$allRolesInId = roleHandler::$listOfRoles2[$this->title];
			if (($allRolesInId&$roleId) > 0)
				return true;
		}
		return false;
	}

	/**
	 * Replaces(if exist, else add) into `$table` with the given Column and Data.
	 * @method replaceTable
	 * @param string $table table Name
	 * @param array(string)      $columnName ColumnNames of login table
	 * @param array(string)      $data       Data Corresponding to ColumnName
	 */
	public function replaceTable($table, $columnName, $data) {
		$fetchDetailsId = 0;
		// Column Query Start
		$sql = 'REPLACE INTO `'. $table .'` (';
		// List all Columns
		for ($i = 0; $i < count($columnName); $i++) {
			if ($i > 0)
				$sql .= ', ';
			// If Column is userId or Id, save it to fetchDetailsId
			if ($columnName[$i] == "userId" || $columnName[$i] == "id") {
				$fetchDetailsId = $data[$i];
			}
			$sql .= '`'. $columnName[$i] .'`';
		}
		$sql .= ') VALUES (';
		// Insert Values
		for ($i = 0; $i < count($columnName); $i++) {
			if ($i > 0)
				$sql .= ', ';
			$sql .= '\''. $data[$i] .'\'';
		}
		$sql .= ')';
		
		// QueryOnce.
		$this->DB->queryOnce($sql);
		// Fetch Details only if same user.
		if ($fetchDetailsId == $this->id) {
			$this->fetchDetails();
		}
	}

	/**
	 * Fetches user details from user_details table and save it
	 * into dynamic variables
	 * Also fetches all previous semesters.
	 * @method fetchDetails
	 */
	private function fetchDetails() {
		$sql = "SELECT * FROM `user_details` WHERE `userId`='". $this->id ."'";
		$this->DB->query($sql);
		if ($this->DB->result->num_rows > 0) {
			$user = $this->DB->result->fetch_assoc();
			foreach($user as $key => $value) {
				$this->$key = $value;
			}
		}
		$tempArray = array();
		// If Student,
		if ($this->title == 1) {
			$sql = "SELECT `semester` FROM `student_semesters` WHERE `user_id`='". $this->id ."'";
			$this->DB->query($sql);
			$this->semArray = array();
			if ($this->DB->result->num_rows > 0) {
				while (($user = $this->DB->result->fetch_assoc())) {
					$this->semArray[] = $user['semester'];
				}
			}
		} else {
			$sql = "SELECT `semester` FROM `teacher_semesters` WHERE `teacher_id`='". $this->id ."'";
			$this->DB->query($sql);
			$this->semArray = array($this->semester);
			if ($this->DB->result->num_rows > 0) {
				while (($res = $this->DB->result->fetch_assoc())) {
					$this->semArray[] = $res['semester'];
				}
			}
		}
		// Remove Duplicate and Current Semester.
		$this->semArray = array_unique($this->semArray);
		$this->semArray = array_diff($this->semArray, array($this->semester));
		// Search Last Key
		$key = array_search(end($this->semArray), $this->semArray);
		// Remove Empty Array's
		for ($i = 0; $i <= $key; $i++) {
			if (!isset($this->semArray[$i])) {
				// Push all elements to left by one position
				for ($j = $i; $j < $key; $j++) {
					$this->semArray[$j] = $this->semArray[$j+1];
				}
				// Remove Last Element of Array
				$key--;
				ksort($this->semArray);
				array_pop($this->semArray);
			}
		}
	}
	
	/**
	 * Updates `login` Table with the given Column and Data.
	 * @method updateTable
	 * @param  array(string)      $columnName ColumnNames of login table
	 * @param  array(string)      $data       Data Corresponding to ColumnName
	 * @param  int      $id         UserID(default: NULL), if NULL, uses current user UserID
	 * @param bool $check Reverify User Data?
	 */
	public function updateTable($columnName, $data, $id = NULL, $check = true) {
		// Column Query Start
		$sql = 'UPDATE `login` SET ';
		// Loop Through all columns, and create SET condition of query.
		for ($i = 0; $i < count($columnName); $i++) {
			if ($i > 0)
				$sql .= ', ';
			// If column is password, hash it, else escape it.
			if ($columnName[$i] == "password") {
				$password = $data[$i];
				$data[$i] = password_hash($data[$i], PASSWORD_BCRYPT);
			} else {
				$this->DB->escape($data[$i]);
			}
			$sql .= $columnName[$i].' = \''. $data[$i] .'\'';
		}
		// If Id is null, use User ID.
		if ($id == NULL)
			$id = $this->id;
		$sql .= ' WHERE `id`=\''. $id .'\'';
		//$this->addInfo($sql);
		// QueryOnce.
		$this->DB->queryOnce($sql);
		if ($check == true) {
			// User Data is changed, so update the Class Details.
			if (isset($password))
				$this->checkUser($this->id, 2, $password);
			else
				$this->checkUser($this->id, 2);
			// Update the Session too.
			$this->updateSession();
		}
	}
	
	/**
	 * Updates Login Session
	 * @method updateSession
	 */
	public function updateSession() {
		$this->startSession();
		$_SESSION['login'] = serialize($this);
		return;
	}
	
	/**
	 * Check if User exists in Database
	 * returns the rows of select query if successful.
	 * @method checkUser
	 * @param  string    $username Username
	 * @param  int       $type     type(0/1/3=Check Username, 2=Check UserID, 3 = Logs the Activity.)
	 * @param  string    $password Check the Password with DB Hashed Password too.
	 * @param bool $updateSession Should register update the session?
	 * @return array               Rows returned from query.
	 */
	public function checkUser($username, $type = 0, $password = NULL, $updateSession = true) {
		// Select Query.
		if ($type == 2) 
			$query = "select * from `login` where `id`='$username'";
		else	// 0 or 1 
			$query = "select * from `login` where `user`='$username'";
		// Execute Query and check for Error.
		$this->DB->query($query);
		$this->DB->errorRedirect();
		// Get Number of Rows
		$rows = $this->DB->result->num_rows;
		// If Password is not null, check the validity of password,
		if ($password != NULL) {
			if ($rows > 0) {
				// Fetch the Columns, and store the decrypted password.
				$user = $this->DB->result->fetch_assoc();
				if ($updateSession)
					$this->decPass = $password;
				// Verify the Password, if failed, set $rows to 0, to avoid furthur execution.
				if (!password_verify($password, $user['password'])) {
					$rows = 0;
					$this->DB->addError("Failed to Validate Password");
				}
				$this->DB->errorRedirect();
				// Check if password needs to be rehashed, if yes, rehash and save it.
				if (password_needs_rehash($user['password'], PASSWORD_BCRYPT)) {
					$newHash = password_hash($password, PASSWORD_BCRYPT);
					$query = "UPDATE `login` SET password='$newHash' where user='$username'";
					$this->DB->query($query);
				}
			}	
		}
		if ($password == NULL)
			$user = $this->DB->result->fetch_assoc();
		// $type >= 1 would update the Login Class with the details.
		if ($type >= 1) {
			if ($rows > 0) {
				if ($updateSession) {
					// Check Branch
					$branchValid = false;
					foreach($this->allBranches as $branchId => $name) {
						if ($name[0] == $user["branch"]) {
							$branchValid = true;
							break;
						}
					}
					if (!$branchValid) {
						$this->addError("Office Staff will verify your account soon. Please try again later.");
						$this->redirect("CMSYS_INDEX");
					}
					// Save Everything to dynamicVariables.
					foreach($user as $key => $value) {
						$this->$key = $value;
					}
					$this->ip = getClientIP();
					//$this->updateSession();
					$this->insertLastIP();
					$this->fetchDetails();
					$this->generateValidSubjects($this->title, $this->id);
					if ($type == 3) {
						$this->updateSession();
						$this->logs->login();
					}
				}
				return $user;
			} else
				return NULL;
		}
		return $user;	
		
	}
	
	/**
	 * Insert Last IP of User into Login Table.
	 * @method insertLastIP
	 */
	public function insertLastIP() {
		$query = "UPDATE `login` SET `ip`='". $this->ip ."' WHERE `id`=". $this->id;
		$this->DB->query($query);
	}
	
	// Send Verification Email to user.
	/**
	 * Sends Verification Email to Registered User
	 * @method email
	 * @param  int $id    UserID
	 * @param string $name Name of User
	 * @param  string $email Email of Registered User
	 */
	public function email($id, $name, $email) {
		// Validity of Verification Code.
		global $codeExpire;
		// Generate Random Code
		$code = substr(md5(mt_rand()), 0, rand(15, 30));
		// Generate the Expiry Time.
		$time = time();
		$eTime = $time+$codeExpire;
		// Insert the code into DB for Email Verification.
		$query = "INSERT INTO verification_code (`user_id`,`code`,`creation_date`,`expiration_date`, `type`) VALUES ($id, '$code', $time, $eTime, 0)";
		$this->DB->query($query);
		
		// Generate Email Message
		$message = "Your Activation Code is ".$code."";
		$to = $email;
		$subject = "Activation Code For CMSys";
		$from = 'dastgirp@gmail.com';
		$body = 'Hello '. $name .',<br/>'.
				'Your Activation Code is '. $code .'.<br/>'.
				' Please Click On <a href="http://dastgir.tech/cmsys/verifyemail.php?id='. $id .'&code='. $code .'"> This link </a> to activate your account.<br/>'.
				'The Link will Expire in '. ($codeExpire/60/60) .' Hours';
		$headers = "From: " . strip_tags($from) . "\r\n";
		$headers .= "Reply-To: ". strip_tags($from) . "\r\n";
		$headers .= "CC: $email\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
		// Mail.
		mail($to, $subject, $body, $headers);
	}

	// Add a New User
	/**
	 * Adds a New User
	 * @method register
	 * @param  string   $username Username
	 * @param  string   $password Decrypted Password
	 * @param  string   $email    Email
	 * @param  string   $name     Name
	 * @param bool $updateSession Should register update the session?
	 */
	public function register($username, $password, $email, $name, $updateSession = true) {
		// Hash the Decrypted Password.
		$passEnc = password_hash($password, PASSWORD_BCRYPT);
		// Prepare and exeucte the query.
		$query = "INSERT INTO login (`user`,`password`,`email`,`name`) VALUES ('$username', '$passEnc', '$email', '$name')";
		$this->DB->query($query);
		// Check the Inserted User
		if ($updateSession == false)
			$rows = $this->checkUser($username, 1, NULL, $updateSession);
		else
			$rows = $this->checkUser($username, 1, $password, $updateSession);
		// Enter the Log
		$this->logs->register($rows['id'], $username, $name);
		// Email Verification Code.
		$this->email($rows['id'], $rows['name'], $email);
		return intval($rows['id']);
	}
	
	/**
	 * Checks if Post Request Exists.
	 * @method check
	 */
	public function check() {
		if (!isset($_POST) || empty($_POST)) {
			$this->addError("No Valid Information Passed.");
			return;
		}
	}
	
	// Check if any of variables in $_POST is empty.
	/**
	 * Checks if any of variables is $_POST is empty
	 * @method checkEmpty
	 * @param  array(string)     $postArguments Key in $_POST
	 * @param  string     $errorMsg      Error Message to be displayed, if any one of them is empty/absent.
	 */
	public function checkEmpty($postArguments, $errorMsg) {
		$anyError = false;
		// Check if Arguemnt is provided or not.
		if (empty($postArguments)) {
			$this->addError("No Arguments Provided for checkEmpty.");
		} else {
			// Check if $_POST request exists.
			if (!isset($_POST) || empty($_POST)) {
				$this->addError("No Valid Information Passed.");
				return;
			}
			$i = 0;
			// Loop through each argument.
			foreach($postArguments as $argument) {
				$i++;
				// If Empty or not set, Output proper error.
				if (!isset($_POST[$argument]) || empty($_POST[$argument])) {
					$this->addError("Argument ". $i ." is not having Valid Information");
					$anyError = true;
				} else 	// Else, set it into $this->G array.
					$this->G[$argument] = $_POST[$argument];
			}
		}
		// If any error is found, add the ErrorMessage.
		if ($anyError)
			$this->addError($errorMsg);
	}
	
	/**
	 * Checks if Email Entered is valid
	 * @method isValidEmail
	 * @param  string       $email Email Address
	 * @return bool                true if valid email is entered, else false.
	 */
	function isValidEmail($email) { 
		return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
	}

	/**
	 * Checks if User Exists in the Database
	 * @method userExists
	 * @param  string     $user Username
	 * @return bool           true if user exists, else false.
	 */
	function userExists($user) {
		$this->DB->escape($user);
		$this->DB->query("SELECT * FROM `login` WHERE `user`='$user'");
		if ($this->DB->result->num_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * Returns the value from `login` table of given specified column
	 * @method getColumnById
	 * @param  string        $column Column to check
	 * @param  string        $userId UserID
	 * @param bool $exception Throw Exception or return null.
	 * @return (any)                 Data in the column(if found), else throws exception.
	 */
	function getColumnById($column, $userId, $exception = true) {
		$this->DB->escape($userId);
		$this->DB->query("SELECT `$column` FROM `login` WHERE `id`='$userId'");
		if ($this->DB->result->num_rows > 0) {
			// Fetch The Row.
			$row = $this->DB->result->fetch_assoc();
			// Return column.
			return $row[$column];
		}
		if ($exception)
			throw new Exception("User Not Found");
		return NULL;
	}
	
	/**
	 * Open DB Connection.
	 * Also Updates the Session.
	 * @method OpenDB
	 */
	public function OpenDB(){
		$this->DB->start();
		$this->updateSession();
	}
	
	// Overriding from errorHandler
	/**
	 * Overriden function from errorHandler
	 * Closes DB and Updates Session before actual redirection.
	 * @method redirect
	 * @param  string   $fileName           Constant of Redirect
	 * @param  string   $additionalRedirect additional URL.
	 * @see RedirectHandler->redirect
	 */
	public function redirect($fileName, $additionalRedirect = NULL) {
		$this->DB->close();
		$this->updateSession();
		parent::redirect($fileName, $additionalRedirect);
	}
}
?>