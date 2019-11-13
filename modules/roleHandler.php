<?php

class RoleHandler {
	/** @var bool Show Debug Message? */
	public static $debug = false;
	/** @var array Array Containing TitleID and Title Name */
	public static $titles = array(	1, 'Student',
									2, 'Teacher',
									4, 'OfficeStaff',
									8, 'Admin',
									16, 'SuperAdmin',
								);
	/**
	 * BlackList Roles for Specific Titles.
	 * These Titles are inherited by Title numbers.
	 * Format:
	 * TitleID => array('ROLE1','ROLE2',....),
	 * @var array
	 */
	public static $blacklist_roles = array(
		1 => array(),
		2 => array('view_attendance', 'view_test_marks',  'view_notes', 'view_assignment', 'view_exam_paper', 'view_notice'),
		4 => array('correct_exam_paper', 'contact_staff', 'add_notes', 'add_assignment', 'list_exam_paper_teacher', 'add_attendance', 'edit_attendance', 'get_exam_paper_image'),
		8 => array(),
		16 => array('delete_attendance'),
	);

	/**
	 * Whitelist Roles for Specific Titles.
	 * These Titles are inherited by Title numbers.
	 * Format:
	 * TitleID => array('ROLE1','ROLE2',....),
	 * @var array
	 */
	public static $whitelist_roles = array(
		1 => array(),
		2 => array(),
		4 => array(),
		8 => array('list_exam_paper_teacher', 'correct_exam_paper', 'get_exam_paper_image'),
		16 => array(),
	);

	/**
	 * Roles to ID Conversion is done here.
	 * Format:
	 * RoleID => 'RoleName',
	 * @var array
	 */
	public static $allRoles = array(
		/** Student */
		 0x00000000001 => 'view_profile',
		 0x00000000002 => 'update_profile',
		 0x00000000004 => 'view_logs',
		 0x00000000008 => 'view_attendance',
		 0x00000000010 => 'view_test_marks',
		 0x00000000020 => 'view_exam_paper',
		 0x00000000040 => 'view_notes',
		 0x00000000080 => 'view_assignment',
		 0x00000000100 => 'view_notice',
		 0x00000000200 => 'view_test_logs',
		 0x00000000400 => 'contact_staff',

		/** Teacher */
		 0x00000000800 => 'add_notice',
		 0x00000001000 => 'add_assignment',
		 0x00000002000 => 'add_notes',
		 0x00000004000 => 'add_attendance',
		 0x00000008000 => 'edit_attendance',
		 0x00000010000 => 'correct_exam_paper',
		 0x00000020000 => 'get_subjects',
		
		/** Office Staff */
		 0x00000040000 => 'add_exam_paper',
		 0x00000080000 => 'add_student',

		/** Admin */
		 0x00000100000 => 'add_staff',
		 0x00000200000 => 'list_student',
		 0x00000400000 => 'view_student',
		 0x00000800000 => 'edit_student',
		 0x00001000000 => 'delete_student',
		 0x00002000000 => 'delete_attendance',
		
		/** Super Admin */
		 0x00004000000 => 'list_staff',
		 0x00008000000 => 'view_staff', 
		 0x00010000000 => 'edit_staff',
		 0x00020000000 => 'delete_staff',
		// List/View/Edit/Delete from List All Page.
		 0x00040000000 => 'list_all',
		'0x00100000000' => 'view_all',
		'0x00200000000' => 'edit_all',
		'0x00400000000' => 'delete_all',

		/** Office Staff */
		'0x00800000000' => 'list_exam_paper',
		'0x01000000000' => 'delete_exam_paper',
		'0x02000000000' => 'list_exam_paper_staff',
		/** Teacher */
		'0x04000000000' => 'list_exam_paper_teacher',
		/** Office Staff */
		'0x08000000000' => 'validate_student',
		/** Student */
		'0x10000000000' => 'get_marks',
		'0x20000000000' => 'get_exam_paper_image',
		/** Teacher */
		'0x40000000000' => 'list_notes_notice_assign',
		'0x80000000000' => 'delete_notes_notice_assign',
	);

	/**
	 * Array containing All the roles with assigned titles.
	 * Roles are inherited by later title's.
	 * Format:
	 * TitleId => array('ValidRoles',...)
	 * @var array
	 */
	public static $roles = array(
		/** Student */
		1 => array( 'view_profile',
					'update_profile',
					'view_logs',
					'view_attendance',
					'view_test_marks',
					'view_exam_paper',
					'view_notes',
					'view_assignment',
					'view_notice',
					'view_test_logs',
					'contact_staff',
					'get_marks',
					'get_exam_paper_image',
				),
		/** Teacher */
		2 => array( 'add_notice',
					'add_assignment',
					'add_notes',
					'add_attendance',
					'edit_attendance',
					'delete_attendance',
					'correct_exam_paper',
					'list_exam_paper',
					'list_exam_paper_teacher',
					'get_subjects',
					'list_notes_notice_assign',
					'delete_notes_notice_assign',
				),
		/** Office Staff */
		4 => array( 'add_exam_paper',
					'list_student',
					'edit_student',
					'add_student',
					'list_exam_paper_staff',
					'delete_exam_paper',
					'validate_student',
				),
		/** Admin */
		8 => array(	'add_staff',
					'view_student',
					'delete_student',
				),
		/** Super Admin */
		16 => array(
					'list_staff',
					'view_staff',
					'edit_staff',
					'delete_staff',
					'list_all',
					'view_all',
					'edit_all',
					'delete_all',
				),
	);
	
	/**
	 * List of Roles converted into ID Format from above all ARrays.
	 * Format:
	 * TitleID => RoleID,
	 * @var array
	 */
	public static $listOfRoles = array();
	/**
	 * Same as $listOfRoles
	 * Only Used in 32 BIT SYSTEMS.
	 * @var array
	 */
	public static $listOfRoles2 = array();
	
	/**
	 * Generates listOfRoles, which would be directly used to check
	 * which all functions can a user do.
	 * @method generateListOfRoles
	 */
	public static function generateListOfRoles(){
		global $is32Bit;
		// Check if listOfRoles is defined or not.
		if (!empty(roleHandler::$listOfRoles)) {
			// If not 32 bit system, or 32bit and ListOfRoles 2 is not empty, return.
			if (!$is32Bit || !empty(roleHandler::$listOfRoles2))
				return;
		}
		// Loop through full Titles
		for ($i = 0; $i < count(roleHandler::$titles); $i += 2) {
			$c = 0;
			// Store TitleID in variable
			$titleId = roleHandler::$titles[$i];
			if (roleHandler::$debug)
				echo "Title: ". roleHandler::$titles[$i+1] ."($titleId)<br/>";
			// TITLE_TEACHER => 2
			// Define for use in other scripts.
			define(strtoupper('TITLE_'. roleHandler::$titles[$i+1]), roleHandler::$titles[$i]);
			// Initiate Array
			roleHandler::$listOfRoles[$titleId] = 0;
			if ($is32Bit)
				roleHandler::$listOfRoles2[$titleId] = 0;
			// Loop through all Titles, and get suitable roles
			for ($o = $titleId; $o >= 1; $o /= 2) {
				// Get Roles as per Title and it's previous titles.
				$roles = roleHandler::$roles[$o];
				if (roleHandler::$debug)
					echo "&nbsp;&nbsp;&nbsp;&nbsp;o:$o</br>";
				// Loop through all Roles.
				for ($j = 0; $j < count($roles); $j++) {
					$skip = false;	// Whitelist the Roles.
					// Loop Through current titles and previous titles.
					for ($k = $titleId; $k >= 1; $k /= 2) {
						// Check BlackListed roles of the title.
						$blacklisted = roleHandler::$blacklist_roles[$k];
						$whitelisted = roleHandler::$whitelist_roles[$k];
						if (in_array($roles[$j], $whitelisted)) {
							$skip = false;
							break;
						}
						// Loop through all blacklisted roles.
						for ($l = 0; $l < count($blacklisted); $l++) {
							// If blacklisted roles found, skip it.
							if ($roles[$j] == $blacklisted[$l]) {
								if (roleHandler::$debug)
									echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Skipped Blacklisted Role: ".$roles[$j]."</br>";
								$skip = true;
								break;
							}
						}
						if ($skip)
							break;
					}
					// If Skip is set to true, just skip it.
					if ($skip)
						continue;
					if (roleHandler::$debug)
						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Added Role: ".$roles[$j]."</br>";
					// Add the RoleID into listOfRoles.
					$valAdd = 0;	// Value to Add in listOfRoles
					// Check if Role is defined.
					if (defined('ROLE_'. strtoupper($roles[$j])))
						$valAdd = constant('ROLE_'. strtoupper($roles[$j]));
					// 32 bit System needs extra processing.
					// Also checks if ROLE2 is present if $valAdd is 0
					if ($is32Bit && $valAdd == 0) {
						if (defined('ROLE2_'. strtoupper($roles[$j])))
							$valAdd = constant('ROLE2_'. strtoupper($roles[$j]));
						else
							throw new Exception("Role Not Found: ". $roles[$j]);
						// Add to 2nd ListOfRoles
						roleHandler::$listOfRoles2[$titleId] += $valAdd;
					} else {
						if ($valAdd == 0)
							throw new Exception("Role Not Found");
						// Add to ListOfRoles
						roleHandler::$listOfRoles[$titleId] += $valAdd;
					}
				}
			}
		}
	}
	
	/**
	 * Converts the Roles into ID.
	 * Also Defines the constant for it.
	 * Constant:
	 * 'ROLE_'
	 * role_name
	 * @method allRolesToId
	 */
	public static function allRolesToId() {
		global $is32Bit;
		foreach(roleHandler::$allRoles as $key => $value) {
			$roleName = 'ROLE_'. strtoupper($value);
			// ROLE_VIEW_PROFILE => 1
			if (($is32Bit) && (gettype($key) == "string" || $key > PHP_INT_MAX)) {
				$roleName = 'ROLE2_'. strtoupper($value);
				$key = (string)$key;	// Convert to String
				$key = substr($key, 2);	// Remove 0x
				$key = str_pad($key, 16, '0', STR_PAD_LEFT);	// Pad till 16th Character
				// Seperate Lower and Upper Bits
				$upper = pack('H8', $key);
				$lower = pack('H8', substr($key, 8));
				// Get Only Upper Bits
				$upperU = unpack('H8', $upper);
				/* We don't need lower bits for now.
				$lowerU = unpack('H8', $lower);
				print($lowerU[1]);
				*/
				$key = $upperU[1];	// Save Upper 16 bits into Key
				$key += 0;	// Convert to Integer
				$key = hexdec($key);
			}
			define($roleName, $key);
		}
	}

	/**
	 * Get TitleName by titleId
	 * @method getTitleName
	 * @param  int       $titleId TitleID
	 * @return string                TitleName("No Title Found", if not found.)
	 */
	public static function getTitleName($titleId) {
		$found = false;
		// Loop through all tiles, and return next index of id.
		foreach(RoleHandler::$titles as $t){
			if ($found == true)
				return $t;
			if ($t == $titleId)
				$found = true;
		}
		return "No Title Found($titleId)";
	}

	/**
	 * Get TitleID by Title Name
	 * @method getTitleId
	 * @param  string     $titleName TitleName
	 * @return int                TitleID(0, if not found.)
	 */
	public static function getTitleId($titleName) {
		$exists = in_array($titleName, RoleHandler::$titles);
		if ($exists) {
			return RoleHandler::$titles[array_search($titleName, RoleHandler::$titles)-1];
		}
		return 0;
	}
}
// Call and Generate ListOfRoles
roleHandler::allRolesToId();
roleHandler::generateListOfRoles();
?>