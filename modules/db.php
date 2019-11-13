<?php
// Required Modules and Configs
require_once(dirname(__FILE__).'/../include.php');

/**
 * Database Class(Extends ErrorHandler Class):
 * This Class contains methods for MySQL Connection, Querying to MySQL,
 * Getting MySQL Errors, Escaping Strings for MySQL and Closing MySQL Connection.
 * @see modules/errorHandler.php
 **/

class DB extends ErrorHandler {
	private $ip;		///< MySQL IP will be stored here
	private $user;		///< MySQL user will be stored here
	private $pass;		///< MySQL Pass will be stored here
	private $db;		///< MySQL DatabaseName will be stored here
	private $mysqli;	///< MySQLi Class will be stored here
	
	// MySQLi Class Related
	public $result;		///< Result Class of Query will be stored here.
	public $running;	///< Tracks whether MySQL Connection is closed

	public $progress;
	
	/**
	 * Returns Last Inserted ID of Query
	 * @method lastId
	 * @return int Last Inserted ID
	 */
	public function lastId() {
		return $this->mysqli->insert_id;
	}

	/**
	 * Initiates MySQL Connection and returns error(if any)
	 * @method start
	 * @return void
	 */
	public function start() {
		$this->user = MYSQL_USER;
		$this->ip = MYSQL_HOST;
		$this->pass = MYSQL_PASS;
		$this->db = MYSQL_DB;
		
		// Connect to MySQL using MySQLi
		$mysqli = new mysqli($this->ip, $this->user, $this->pass);
		if ($mysqli->connect_errno) {
			$this->addError("Sorry, this website is experiencing problems.");
			$this->addError("Please try again after sometime(". $mysqli->connect_errno .").");
			//$this->addError("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
			return;
		}
		// Create database
		$sql = "CREATE DATABASE IF NOT EXISTS ". $this->db ."";
		if ($mysqli->query($sql) !== TRUE) {
			$this->addError("Sorry, this website is experiencing problems.");
			$this->addError("Please try again after sometime(". $mysqli->connect_errno .").");
			//$this->addError("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
			return;
		}
		$mysqli->select_db($this->db);
		$this->mysqli = $mysqli;
		$this->running = true;
		return;
	}
	
	/**
	 * Inititates MySQL Connection and returns error(if any)
	 * @method __construct
	 */
	function __construct() {
		$this->running = false;
		$this->mysqli = NULL;
		$this->result = NULL;
		$this->start();
	}
	
	/**
	 * Escapes the String, Needs Active MySQL connection for escape to be successful.
	 * @method escape
	 * @param  string &$variable Variable to escape(Passed by reference)
	 */
	function escape(&$variable) {
		if ($this->running == false || !empty($this->mysqli)) {
			$this->start();
		}
		$variable = stripslashes($variable);
		$variable = $this->mysqli->real_escape_string($variable);
	}
	
	/**
	 * Runs Query and restores MySQL State as it was before running Query.
	 * @method queryOnce
	 * @param  string    $query Query to be executed.
	 */
	function queryOnce($query) {
		$status = $this->running;
		$this->query($query);
		if (!$status)
			$this->close();
	}
	
	/**
	 * Runs Query and Starts MySQL(if not yet started).
	 * Can Run Multiple Query(or full file contents).
	 * @method multiQuery
	 * @param  string     $query Query to be executed.
	 */
	function multiQuery($query) {
		$this->freeResult();
		if ($this->running == false || !empty($this->mysqli)) {
			$this->start();
		}
		if (!($this->result = $this->mysqli->multi_query($query))) {
			$this->addError("Sorry, this website is experiencing problems.");
			$this->addError("Please try again after sometime(". $this->mysqli->errno .").");
			//$this->addError($this->mysqli->error);
			$this->close();
			//throw New Exception("StackTrace");
		}
		$this->progress++;
	}
	
	/**
	 * Runs Query and starts MySQL if not yet started.
	 * Capable of Executing only 1 query.
	 * @method query
	 * @param  string $query Query to be executed
	 */
	function query($query) {
		$this->freeResult();
		if ($this->running == false || !empty($this->mysqli)) {
			$this->start();
		}
		if ($this->mysqli == NULL ||
			!($this->result = $this->mysqli->query($query))) {
			$this->addError("Sorry, this website is experiencing problems.");
			$this->addError("Please try again after sometime(". $this->mysqli->errno .").");
			//$this->addError($query);
			//$this->addError($this->mysqli->error);
			$this->close();
			//throw New Exception("StackTrace");
		}
	}

	/**
	 * PageQuery: Generates a Query which can be used for Paging Related Tasks.
	 * This would just need the original query, and always sorts by id.
	 * @method pageQuery
	 * @param  array    $queries  [0]=>Query to be executed(SELECT QUERY) [1]=> Count Query
	 * @param  int       $page    page number(Starts from 0)
	 * @param  int       $step    Step(Default to 10)
	 * @return array(result, result)             Returns Result of MySQL, [0]=>Actual Query [1]=>TotalCount.
	 */
	public function pageQuery($queries, $page = 0, $step = 10) {
		if ($this->running == false || !empty($this->mysqli))
			$this->start();
		// Calculate Start Index
		$start = $page*$step;
		// Prepare Query.
		$query = $queries[0]." ORDER BY `id` ASC LIMIT $start, $step";	// Actual Query
		if (($this->result = $this->mysqli->query($query))) {
			/** @var result Result 1 */
			$res1 = $this->result;
			// Count Query
			$query = $queries[1];	// Count Query
			if (($this->result = $this->mysqli->query($query))) {
				/** @var result Result 2 */
				$res2 = $this->result->fetch_all();
				return array($res1, $res2);	// Return
			}
		}
		return NULL;
	}
	
	/**
	 * Free's the Result given by SELECT query.
	 * @method freeResult
	 */
	function freeResult() {
		if ($this->result == true)
			return;
		if ($this->result != NULL &&
			$this->result->num_rows > 0) {
			$this->result->free();
			$this->result = NULL;
		}
	}
	
	/**
	 * Closes any open MySQL Connection and free's the Result(if any)
	 * @method close
	 */
	function close() {
		$this->freeResult();
		if ($this->mysqli != NULL && isset($this->mysqli->close)) {
			$this->mysqli->close();
		}
		$this->mysqli = NULL;
		$this->running = false;
	}
	
	/**
	 * Overriden function: Closes DB connection before redirection
	 * @method redirect
	 * @param  string   $fileName           Redirect Path(@see "modules/redirectHandler.php")
	 * @param  string/int   $additionalRedirect Addtional Redirect
	 */
	public function redirect($fileName, $additionalRedirect = NULL) {
		$this->close();
		parent::redirect($fileName, $additionalRedirect);
	}
}
?>