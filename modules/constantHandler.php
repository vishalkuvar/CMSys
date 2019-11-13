<?php

/**
 * ConstantHandler Class:
 * For Easy Handling and definitions of Constants.
 */
class ConstantHandler {
	/**
	 * Stores the Next Constant ID
	 * @var integer
	 */
	private $constantId;

	/**
	 * Constructor:
	 * Set's the Starting Constant ID
	 * @method __construct
	 * @param  int         $start Starting Constant ID
	 */
	public function __construct($start = 1) {
		$this->constantId = $start;
	}

	/**
	 * Defines the New Constant with constantID and increments the ID
	 * @method define
	 * @param  va_arg()	Variable Arguments
	 */
	public function define() {
		$numArgs = func_num_args();

		if ($numArgs < 1) {
			throw New Exception("No Constants Given");
		}

		$args = func_get_args();
		foreach ($args as $index => $arg) {
			if (defined($arg)) {
				throw new Exception("Constant $arg is already defined.");
			}
			define($arg, $this->constantId++);
			unset($args[$index]);
		}
	}
}
?>