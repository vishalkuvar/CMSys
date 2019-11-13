<?php
require_once(dirname(__FILE__).'/../include.php');

//<!-- Pushy Menu -->
/**
 * Navigation Class(Uses Pushy Navigation)
 */
class Navigation {
	/**
	 * Constructor:
	 * 	Start's the Navigation Tag.
	 * @method __construct
	 */
	function __construct() {
		$this->start();
	}
	
	/**
	 * Initial Navigation Tag
	 * @method start
	 */
	function start() {
		echo '<nav class="pushy pushy-left">';
		echo '<ul>';
	}
	
	/**
	 * Navigation Tag Ends.
	 * @method end
	 */
	function end() {
		echo '</ul>';
		echo '</nav>';
	}

	/**
	 * Add a Seperator.
	 * @param int $marginRight `x` px margin at right side.
	 * @method sep
	 */
	function sep($marginRight = 0) {
		if ($marginRight > 0)
			echo '<hr class="style1" style="margin-right: '. intval($marginRight) .'px;">';
		else
			echo '<hr class="style1">';
	}
	
	// Add a Link in the Navigation Bar
	/**
	 * Add a Link into the Navigation Menu.
	 * @method list_
	 * @param  string $name Name to be Shown in link
	 * @param  string $link Link to be redirected into(Can be Constant).
	 */
	function list_($name, $link = "#") {
		if (defined($link)) {
			$link = RedirectHandler::getRedirectURL($link);
		}
		echo '<li class="pushy-link"><a href="'. $link .'">'. $name .'</a></li>'; 
	}
	
	/**
	 * Parses the Action Array, to either call list_ or sep
	 * @method parseSingle
	 * @param  array(string/array)      $array Array containing all Actions
	 * @param int $nested Is this menu on submenu?
	 * $array[0] = $name;
	 * $array[1] = $link;
	 */
	function parseSingle($array, $nested) {
		if ($array[0] == "#") {
			if ($nested > 0)
				$this->sep(5);
			else
				$this->sep();
		} else {
			$this->list_($array[0], $array[1]);
		}
	}
}

/**
 * NavigationSubMenu Class
 * It Extends Above Navigation Class.
 */
class NavigationSubMenu extends Navigation {
	
	/**
	 * Constructor:
	 * Creates the Header of Submenu if name is provided
	 * @method __construct
	 * @param  string      $name Name of SubMenu
	 */
	function __construct($name = null) {
		if ($name != null)
			$this->startSub($name);
	}
	
	/**
	 * Starts the SubMenu
	 * @method startSub
	 * @param  string   $name Name of SubMenu
	 */
	function startSub($name = null) {
		echo '<li class="pushy-submenu">';
		$this->header($name);
	}
	
	// Title of SubMenu
	/**
	 * Shoows the Header of SubMenu(This is expanded into submenu's)
	 * @method header
	 * @param  string $name Name of SubMenu
	 */
	function header($name) {
		if ($name != null) {
			echo '<a href="#">'. $name .'</a>';
			echo '<ul>';
		}
	}
	
	/**
	 * End's the SubMenu
	 * @method endSub
	 */
	public function endSub() {
		echo '</ul>';
		echo '</li>';
	}
	
	/**
	 * Checks If Action can be performed by given roles,
	 * hides the action if user cannot perform the role.
	 * @method checkArray
	 * @param  array     $actions Array containing Action List
	 * @param  int        $nested  How Deep is SubMenu Nested
	 * @return (array,array,array,array)              result,ValidResult,finalResult,resultSummarized.
	 * $result[]: Nested Array containing true/false of menu/submenu.
	 * $validResult[]: Same as $result[].
	 * $finalResult: true if even one menu can be displayed.
	 * $resultSummarized[]: true if Menu/SubMenu can be displayed. It summarizes submenu into 1 .
	 */
	function checkArray($actions, $nested = 0) {
		global $login;	// Global Login for roleValid
		// Initialize Some Variables.
		$result = array();
		$resultSummarized = array();
		$sepResult = false;
		$validResult = array();
		$finalResult = false;

		// Parse and Check first, if any elements can be displayed
		// Loop through all Actions.
		for ($i = 0; $i < count($actions); $i++) {
			// Check if link is array.
			if (is_array($actions[$i][1])) {
				//If it's array, check the array with self function and increased nest.
				$display = $this->checkArray($actions[$i][1], $nested+1);
				// Set the related Results into outer array, to return at nested level 0.
				$validResult[$i] = $display[1];
				$result[$i] = $display[0];
				$resultSummarized[$i] = false;
				// if Final Result is true, set the Other Results to true.
				if ($display[2]) {
					$resultSummarized[$i] = true;
					$sepResult = true;
					$finalResult = true;
					continue;
				}
			} else {
				// Set ValidResult and result to false.
				$validResult[$i] = false;
				$result[$i] = false;
				// If Count is 2, there's no role.
				if (count($actions[$i]) == 2) {
					// 1st param # = Seprator
					if ($actions[$i][0] == "#") {
						// if SepratorResult is true, then only display the seperator.
						if ($sepResult == false) {
							continue;
						}
						$sepResult = false;
					}
					// Menu is visible, set the Result to true.
					$validResult[$i] = true;
					$result[$i] = true;
					$finalResult = true;
					$sepResult = true;
				} else if ($login->roleValid($actions[$i][2])) {	// Check if role is valid for current user, if yes, then set result to true.
					$validResult[$i] = true;
					$result[$i] = true;
					$sepResult = true;
					$finalResult = true;
				}
				// resultSummarized is set to $result.
				$resultSummarized[$i] = $result[$i];
				continue;
			}
		}
		return array($result, $validResult, $finalResult, $resultSummarized);
	}
	
	/**
	 * Parses List of Actions to be displayed on Navigation Bar
	 **/
	/**
	 * Parses List of Actions to be displayed on Navigation Menu
	 * @method parseArray
	 * @param  array     $actions    List of Actions
	 * @param  bool     $res        Result of Menu/SubMenu
	 * @param  array(bool)     $canDisplay Array containing results if menu/submenu can be displayed.
	 * @param int $nested How Deep is self recursion.
	 */
	function parseArray($actions, $res = null, $canDisplay = null, $nested = 0) {
		global $login;
		// If $res is null, call checkArray.
		if ($res == null) {
			$resVal = $this->checkArray($actions);
			// Save the Result to parse it.
			$res = $resVal[1];
			$canDisplay = $resVal[0];
			$canDisplayArray = $resVal[3];
		}
		
		//var_dump($canDisplay);
		// Loop through all Actions..
		for ($i = 0; $i < count($actions); $i++) {
			//print("$i:". $actions[$i][0] .":". count($actions) .":". count($canDisplay) ."<br/>");
			// Check if menu/submenu can be displayed.
			if (!$canDisplay[$i] || (isset($canDisplayArray[$i]) && !$canDisplayArray[$i]))
				continue;
			// If 1st index is array, it's submenu, call SubMenu action.
			if (is_array($actions[$i][1])) {
				$this->startSub($actions[$i][0]);
				$this->parseArray($actions[$i][1], $res[$i], $canDisplay[$i], $nested+1);
				$this->endSub();
			} else {	// Else it's menu.
				if ($res[$i] == false)	// If Result is false, don't display.
					continue;
				// Check if role Exists, if yes, check it and print the Menu
				if (count($actions[$i]) == 2 || (count($actions[$i]) == 3 && $login->roleValid($actions[$i][2])))
					$this->parseSingle($actions[$i], $nested);
			}
		}
	}
}
?>