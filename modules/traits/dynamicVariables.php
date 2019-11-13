<?php
/**
 * This Trait will handle stuff related to Dynamic Variables.
 * Class Extending this Trait will get power of using dynamic variables.
 * This variables will be auto defined as public/private/deprecated, and throw
 * suitable error if any violation is found.
 **/
 
trait DynamicVariables {
	/** @var array Dynamic Data is stored */
	private $dynamicVarData = array();
	/** @var array Public/Private/Deprecated Variable names are saved for reference to __get,__isset,__set */
	private $save = array(
					'public' => array(
										// Login Table
										'id', 'user', 'email', 'name', 'verified', 'title', 'semester', 'branch', 'ip',
										// user_details Table
										'address', 'pincode', 'mobile_no', 'address2', 'pincode2', 'mobile_no2', 'sex', 'date', 'birth_place', 'religion', 'caste', 'category', 'blood_group'
									),
					'private' => array('password'),
					'deprecated' => array('titleid'),
					);
	
	/**
	 * Magic __isset function, get is called when someone tries to access variable.
	 * This Checks for Dynamic Variable and return if found.
	 * @method __isset
	 * @param  string  $varName Variable Name
	 * @return bool             true if variable is defined, else false
	 */
	public function __isset($varName) {
		// Checking if Function was called from inside or outside class.
		$trace = debug_backtrace();
		$caller = array_shift($trace);
		$insideClass = true;
		if (empty($trace)){
			$insideClass = false;
		}
		// Check if deprecated variable is called.
		if (in_array($varName, $this->save['deprecated']))
			return false;
		
		if (!$insideClass) {	// IF not called from inside Class, check for private variables.
			if (in_array($varName, $this->save['private']))
				return false;
		}

		// Check if variable exists in $data
		if (!array_key_exists($varName, $this->dynamicVarData)){
			// Attribute is not defined, check if the variable really exists in class.
			if (isset($this->$varName)) {
				return true;
			}
			return false;
		}
		else	// Return the Dynamic Variable
			return true;

	}

	/**
	 * Magic __Get function, get is called when someone tries to access variable.
	 * This Checks for Dynamic Variable and return if found.
	 * @method __get
	 * @param  string  $varName Variable Name
	 * @return string             variable contents
	 */
	public function __get($varName) {
		// Checking if Function was called from inside or outside class.
		$trace = debug_backtrace();
		$caller = array_shift($trace);
		$insideClass = true;
		if (empty($trace)){
			$insideClass = false;
		}
		// Check if deprecated variable is called.
		if (in_array($varName, $this->save['deprecated']))
			throw new Exception("Deprecated Variable '$varName' is used");
		
		if (!$insideClass) {	// IF not called from inside Class, check for private variables.
			if (in_array($varName, $this->save['private']))
				throw new Exception("Private Variable '$varName' is Called.");
		}

		// Check if variable exists in $data
		if (!array_key_exists($varName, $this->dynamicVarData)){
			// Attribute is not defined, check if the variable really exists in class.
			if (isset($this->$varName)) {
				return $this->$varName;
			}
			throw new Exception("NonExistent Variable '$varName' is Called.");
		}
		else	// Return the Dynamic Variable
			return $this->dynamicVarData[$varName];

	}
	
	/**
	 * Magic __Set function, set is called when someone tries to set a variable.
	 * Sets the Dynamic variable if no violation is found.
	 */
	/**
	 * Magic __Set function, set is called when someone tries to set a variable.
	 * Sets the Dynamic Variable if no violation is found.
	 * @method __set
	 * @param  string $varName Variable Name
	 * @param  string $value   Value for Variable
	 */
	public function __set($varName,$value) {
		// Checking if Function was called from inside or outside class.
		$trace = debug_backtrace();
		$caller = array_shift($trace);
		$insideClass = true;
		if (empty($trace)){
			$insideClass = false;
		}
		
		// Check if Deprecated variable is been set.
		if (in_array($varName, $this->save['deprecated']))
			throw new Exception("Deprecated Variable '$varName' is been set");
		
		// Store public variables in temp var.
		$variables = $this->save['public'];
		// If called form inside Class, merge the private variables too.
		if ($insideClass) {
			$variables = array_merge($variables, $this->save['private']);
		} else {	// If Called from outside class, check private and throw error if use violated.
			if (in_array($varName, $this->save['private'])) {
				throw new Exception("Trying to Set Private Variable $varName .");
			}
		}
		// If in variable, then set the dynamic variable.
		if (in_array($varName, $variables))
			$this->dynamicVarData[$varName] = $value;
		// Update Login Session.
		$this->updateSession();
	}
}
?>