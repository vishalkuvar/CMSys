<?php
require_once(dirname(__FILE__).'/../include.php');

/**
 * SQLAutoUpdater:
 * Updates the SQL from sql/ folder.
 * One file is executed only once.
 * All Files from sql/ are executed and saved into table so that same file isn't executed twice.
 */
class SQLAutoUpdater {
	/** @var class DatabaseClass(@see DB) */
	private $db;
	/** @var array List of Files to be executed. */
	private static $files;
	/** @var string Directory of sql/ folder. */
	private $dir;

	/**
	 * Constructor:
	 * Generates Directory, Initiates DB Class.
	 * Checks for Executed SQL Files, and executes new SQL File(if any)
	 * @method __construct
	 */
	function __construct() {
		// Initiates Directory and DB.
		$this->dir = dirname(__FILE__).'/../sql/';
		$this->db = new DB();

		// In case of error, don't proceed.
		if ($this->db->isError())
			return;
		// Check if sql_version table exists.
		$this->db->query("SHOW TABLES LIKE 'sql_version'"); 
		$tempFiles = array();
		//var_dump($this->db->result);
		if ($this->db->result && $this->db->result->num_rows == 0) {
		} else {	// If exists, select aready executed files.
			$this->db->query("SELECT * FROM `sql_version`");
			if ($this->db->result) {
				if ($this->db->result->num_rows > 0) {
					while ($user = $this->db->result->fetch_assoc()) {
						// Save the Already executed files into $tempFiles.
						$tempFiles[] = $user['name'];
					}
				}
			}
		}
		// Initialize List of Files in sql/ folder.
		SQLAutoUpdater::$files = array();
		$dir = opendir($this->dir);	// Open Directory of SQL Files
		// Generate Files in sql/ folder.
		while(false != ($file = readdir($dir))) {
			// Ignore some common files.
			if(($file != ".") and ($file != "..") and ($file != "index.php")) {
				// Exclude the already executed files.
				if (!in_array($file, $tempFiles))
					SQLAutoUpdater::$files[] = $file;
			}   
		}

		// Sort The Files to be exeucted in Alphabetically order(case-insensitive.)
		natcasesort(SQLAutoUpdater::$files);
		$this->db->progress = 0;
		// Execute and Put file names into DB
		for ($i = 0; $i < count(SQLAutoUpdater::$files); $i++) {
			$sleepCounter = 0;
			$fileName = SQLAutoUpdater::$files[$i];
			
			$dir = $this->dir;
			$contents = file_get_contents($dir . $fileName);
			$progress = $this->db->progress;
			$this->db->multiQuery($contents);
			do {
				sleep(3);
				$sleepCounter++;
				if ($sleepCounter > 12) {
					die("Too much wait");
				}
			} while ($progress == $this->db->progress);
			if ($this->db->result && $fileName != "base.sql") {
				$this->db->query("INSERT INTO `sql_version` (`name`) VALUES ('$fileName');");
			}
		}
		return;
	}
}
// Execute the AutoUpdater
$class_SQLAutoUpdater = new SQLAutoUpdater();
?>