<?php
/**
 * Contains Redirect Constant, URL and Role
 * Some Constants are defined as mentioned below:
 * Constant: As mentioned.
 * URL: 'CONSTANT'_URL
 * Role: 'CONSTANT'_ROLE
 * Format:
 * 	'CONSTANT', 'path/to/file/without/extension/', 'role_name', 'Title Name'
 * 	Constant: Can be Any Constant
 * 	path/to/file/: Should contain full path of file from 'cms' folder and should end with '/'
 * 	role_name: Should be same as defined in roleHandler
 * 	TitleName: This would be shown on title bar and top of page. 
 * @var array
 */
$redirect_indexes = array(
							/** Accounts */
							'CMSYS_VIEW_PAPER_STUDENT', 'account/paper/viewStudent/', 'view_exam_paper', 'View Exam Paper',
							// Marks
							'CMSYS_VIEW_MARKS', 'account/marks/view/', 'view_test_marks', 'View Marks',
							// Validate User
							'CMSYS_LIST_STUDENT_VERIFY', 'account/member/list/studentValidate/', 'validate_student', 'Validate Student',
							// Paper
							'CMSYS_LIST_PAPER', 'account/paper/list/', 'list_exam_paper', 'List Exam Paper',
							'CMSYS_LIST_PAPER_STUDENT_STAFF', 'account/paper/listStudentStaff/', 'list_exam_paper_staff', 'Students Appeared for Exams',
							'CMSYS_LIST_PAPER_STUDENT_TEACHER', 'account/paper/listStudentTeacher/', 'list_exam_paper_teacher', 'Students Appeared for Exams',

							// Check Paper
							'CMSYS_CHECK_PAPER', 'account/paper/check/', 'correct_exam_paper', 'Check Exam Paper',
							'CMSYS_CHECK_PAPER_FINAL', 'account/paper/checkFinal/', 'correct_exam_paper', 'Finalize Exam Paper',

							// Add Paper
							'CMSYS_ADD_PAPER2', 'account/files/paper2/', 'add_exam_paper', 'Upload Exam Paper',
							'CMSYS_ADD_PAPER', 'account/files/paper/', 'add_exam_paper', 'Add Exam Paper',

							'CMSYS_VIEW_PAPER', 'account/paper/view/', 'correct_exam_paper', 'View Exam Paper',
							'CMSYS_SAVE_PAPER', 'account/paper/save/', 'correct_exam_paper', 'Save Exam Paper',

							'CMSYS_CONTACT_STAFF', 'account/contactStaff/', 'contact_staff', 'Contact Staff',	// Symbols not supported as of now
							// Test Logs
							'CMSYS_VIEW_TEST_LOGS', 'account/test/', 'view_test_logs', 'View Test Logs',
							// List/View/Delete
							'CMSYS_LIST_STUDENT', 'account/member/list/student/', 'list_student', 'List Student',
							'CMSYS_VIEW_STUDENT', 'account/member/view/student/', 'view_student', 'View Student',
							'CMSYS_EDIT_STUDENT', 'account/member/edit/student/', 'edit_student', 'Edit Student',

							'CMSYS_LIST_TEACHER', 'account/member/list/teacher/', 'list_staff', 'List Teacher',
							'CMSYS_VIEW_TEACHER', 'account/member/view/teacher/', 'view_staff', 'View Teacher',
							'CMSYS_EDIT_TEACHER', 'account/member/edit/teacher/', 'edit_staff', 'Edit Teacher',

							'CMSYS_LIST_STAFF', 'account/member/list/staff/', 'list_staff', 'List Staff',
							'CMSYS_VIEW_STAFF', 'account/member/view/staff/', 'view_staff', 'View Staff',
							'CMSYS_EDIT_STAFF', 'account/member/edit/staff/', 'edit_staff', 'Edit Staff',

							'CMSYS_LIST_ALL', 'account/member/list/all/', 'list_all', 'List All Members',
							'CMSYS_VIEW_ALL', 'account/member/view/all/', 'view_all', 'View Member',
							'CMSYS_EDIT_ALL', 'account/member/edit/all/', 'edit_all', 'Edit Member',

							'CMSYS_ADD_STUDENT', 'account/member/add/student/', 'add_student', 'Add Student',
							'CMSYS_ADD_STAFF', 'account/member/add/staff/', 'add_staff', 'Add Staff',

							// Notice/Notes/Assignments
							'CMSYS_ADD_NOTICE', 'account/files/upload/notice/', 'add_notice', 'Add Notice',
							'CMSYS_ADD_NOTES', 'account/files/upload/notes/', 'add_notes', 'Add Notes',
							'CMSYS_ADD_ASSIGNMENT', 'account/files/upload/assignment/', 'add_assignment', 'Add Assignment',

							'CMSYS_LIST_FILES', 'account/files/list/', 'list_notes_notice_assign', 'List Files',

							'CMSYS_VIEW_NOTICE', 'account/files/view/notice/', 'view_notice', 'View Notice',
							'CMSYS_VIEW_NOTES', 'account/files/view/notes/', 'view_notes', 'View Notes',
							'CMSYS_VIEW_ASSIGNMENT', 'account/files/view/assignment/', 'view_assignment', 'View Assignment',
							
							// Attendance
							'CMSYS_VIEW_ATTENDANCE', 'account/attendance/view/', 'view_attendance', 'View Attendance',
							'CMSYS_ADD_ATTENDANCE', 'account/attendance/add/', 'add_attendance', 'Add Attendance',
							'CMSYS_ADD_ATTENDANCE2', 'account/attendance/add2/', 'add_attendance', 'Mark Attendance',
							'CMSYS_EDIT_ATTENDANCE', 'account/attendance/edit/', 'add_attendance', 'Mark Attendance',
							'CMSYS_SAVE_ATTENDANCE', 'account/attendance/save/', 'add_attendance', 'Save Attendance',
							'CMSYS_LIST_ATTENDANCE', 'account/attendance/list/', 'add_attendance', 'List Attendance',

							// Updation
							'CMSYS_UPDATE_PROFILE', 'account/profile/update/', 'update_profile', 'Update Profile',

							'CMSYS_LOGS', 'account/logs/', 'view_logs', 'View Logs',
							'CMSYS_PROFILE', 'account/profile/', 'view_profile', 'Home Page',
							/** Home */
							'CMSYS_INDEX', 'index/', '', 'Login',
							'CMSYS_REGISTER', 'register/', '', 'Register',
							'CMSYS_VERIFY_EMAIL', 'system/verify/email/', '', 'Verify Email',

							
							/** System */
							// Attendance
							'CMSYS_SYSTEM_DELETE_ATTENDANCE', 'system/delete/attendance/', 'delete_attendance', 'Delete Attendance',
							'CMSYS_SYSTEM_GET_ATTENDANCE', 'system/sync/attendance/', 'view_attendance', 'Get Attendance',
							// Student Validation
							'CMSYS_SYSTEM_STUDENT_VERIFY', 'system/verify/member/', 'validate_student', 'Validate Student',
							// Notice/Notes/Assignment
							'CMSYS_SYSTEM_ADD_NOTICE', 'system/add/files/notice/', 'add_notice', 'Add Notice',
							'CMSYS_SYSTEM_ADD_NOTES', 'system/add/files/notes/', 'add_notes', 'Add Notes',
							'CMSYS_SYSTEM_ADD_ASSIGNMENT', 'system/add/files/assignment/', 'add_assignment', 'Add Assignment',
							'CMSYS_SYSTEM_DELETE_FILE', 'system/delete/files/', 'delete_notes_notice_assign', 'Delete File',

							// Subjects
							'CMSYS_GET_MARKS', 'system/get/marks/', 'get_marks', 'Get Marks',
							'CMSYS_GET_SUBJECTS', 'system/get/subjects/', 'get_subjects', 'Get Subject',

							// Paper
							'CMSYS_SYSTEM_SYNC_MARKS', 'system/sync/marks/', 'correct_exam_paper', 'Sync Marks',
							'CMSYS_SYSTEM_ADD_PAPER', 'system/add/paper/', 'add_exam_paper', 'Add Exam Paper',
							'CMSYS_SYSTEM_DELETE_PAPER', 'system/delete/paper/', 'delete_exam_paper', 'Delete Exam Paper',
							'CMSYS_GET_PAPER', 'system/get/paperImage/', 'get_exam_paper_image', 'Get Exam Paper Image',

							// Member
							'CMSYS_SYSTEM_EDIT_STUDENT', 'system/update/member/student/', 'edit_student', 'Edit Student',
							'CMSYS_SYSTEM_DELETE_STUDENT', 'system/delete/member/student/', 'delete_student', 'Delete Student',

							'CMSYS_SYSTEM_EDIT_TEACHER', 'system/update/member/teacher/', 'edit_staff', 'Edit Teacher',
							'CMSYS_SYSTEM_DELETE_TEACHER', 'system/delete/member/teacher/', 'delete_staff', 'Delete Teacher',

							'CMSYS_SYSTEM_EDIT_STAFF', 'system/update/member/staff/', 'edit_staff', 'Edit Staff',
							'CMSYS_SYSTEM_DELETE_STAFF', 'system/delete/member/staff/', 'delete_staff', 'Delete Staff',

							'CMSYS_SYSTEM_EDIT_ALL', 'system/update/member/all/', 'edit_all', 'Edit Member',
							'CMSYS_SYSTEM_DELETE_ALL', 'system/delete/member/all/', 'delete_all', 'Delete Member',

							'CMSYS_SYSTEM_ADD_STUDENT', 'system/add/member/student/', 'add_student', 'Add Student',
							'CMSYS_SYSTEM_ADD_STAFF', 'system/add/member/staff/', 'add_staff', 'Add Staff',
							// Update Profile
							'CMSYS_SYSTEM_UPDATE_PICTURE', 'system/update/picture/', 'update_profile', 'Update Picture',
							'CMSYS_SYSTEM_UPDATE_DETAILS', 'system/update/details/', 'update_profile', 'Update Details',
							'CMSYS_SYSTEM_UPDATE_PASSWORD', 'system/update/password/', 'update_profile', 'Update Password',
							'CMSYS_SYSTEM_UPDATE_PROFILE', 'system/update/profile/', 'update_profile', 'Update Profile',

							// View Notes/Notice/Assignment
							'CMSYS_SYSTEM_VIEW_NOTICE', 'system/view/files/notice/', 'view_notice', 'View Notice',
							'CMSYS_SYSTEM_VIEW_NOTES', 'system/view/files/notes/', 'view_notes', 'View Notes',
							'CMSYS_SYSTEM_VIEW_ASSIGNMENT', 'system/view/files/assignment/', 'view_assignment', 'View Assignment',

							'CMSYS_LOGIN', 'system/login/', '', 'Login',
							'CMSYS_LOGOUT', 'system/logout/', '', 'Logout',
							/** Internal Register/Login */
							'CMSYS_SYSTEM_REGISTER', 'system/register/', '', 'Register',
							'CMSYS_SYSTEM_LOGIN', 'system/login/', '', 'Login',
						);
/**
 * Converts Above Array into Constant Format.
 * @method assign_redirect
 * @note Edit .htaccess if you edit something here.
 */
function assign_redirect() {
	global $redirect_indexes;
	$j = 0;
	for ($i = 0; $i < count($redirect_indexes); $i = $i+4) {
		define($redirect_indexes[$i], $j++);
		define($redirect_indexes[$i].'_URL', $redirect_indexes[$i+1]);
		define($redirect_indexes[$i].'_ROLE', $redirect_indexes[$i+2]);
		define($redirect_indexes[$i].'_TITLE', $redirect_indexes[$i+3]);
	}
}

assign_redirect();

/**
 * RedirectHandler Class:
 * Contains All Methods for redirection.
 */
class RedirectHandler {

	/**
	 * Static Function:
	 * Generates the Additional URL
	 * Returns same url if $additional is NULL.
	 * @method getAdditionalURL
	 * @param  string           $url        URL(string)
	 * @param  string           $additional Additional Parameter
	 * @return string                       Final URL
	 */
	public static function getAdditionalURL($url, $additional) {
		if ($additional == NULL) {
			return $url;
		} else {
			$additional .= '/';
			if ($url[strlen($url)-1] == '/') {
				return ($url . $additional);
			} else {
				return ($url .'/'. $additional);
			}
		}
	}
	
	/**
	 * Return's RedirectURL along with host.
	 * @method getRedirectURL
	 * @param  string         $redirectCode Constant for Redirection.
	 * @param  string         $additional   Additional Paramater(default: NULL)
	 * @return string                       Host with url
	 */
	public static function getRedirectURL($redirectCode, $additional = NULL) {
		global $rootDir;
		$url = constant($redirectCode .'_URL');
		$url = RedirectHandler::getAdditionalURL($url, $additional);
		$dir = $rootDir .'/'. $url;
		return $dir;
	}

	/**
	 * Redirects the browser with given parameters
	 * @method redirect
	 * @param  string   $redirectCode Redirect Constant
	 * @param  string   $additional   Additional Parameter(default: NULL)
	 */
	public static function redirect($redirectCode, $additional = NULL) {
		if (defined($redirectCode)) {
			redirect(constant($redirectCode .'_URL'), $additional);
		}
	}
	
	/**
	 * Generates the file name to include for template based on the url given.
	 * url doesn't include host.
	 * It tries to best match the url with file name and avoids additional parameters
	 * @method getIncludeName
	 * @param  string         $url URL(without host)
	 * @return array(s,s)              array(Full FileName, FirstMatchArray(PregMatch))
	 */
	public static function getIncludeName($url) {
		// Count Number of slashes
		$count = substr_count($url, "/");
		// Slashes <= 1 => No URL Provided.
		if ($count <= 1)
			return "";
		// Base URL to check.
		$string = "/cmsys\/";
		// Check Last 5 Matches Matches.
		$min = max(0, $count-5);
		// Find the First Index
		$j = $count-$min;
		// Loop through max-min for generating matches in descending order.
		for ($i = $count; $i >= $min; $i--) {
			$string .= "([^\/]+)\/";
			$strings[$j--] = $string .'/';
		}
		// Initialize Variables.
		$firstMatch = NULL;
		$fileName = '';
		// Loop reverse(0-max)
		for ($j = 0; $j <= $count-$min; $j++) {
			// Match the Strings generated with url, and generate the matches.
			if (preg_match($strings[$j], $url, $matches)) {
				// if firstMatch is null, copy matches to it.
				if ($firstMatch == NULL) {
					$firstMatch = $matches;
				}
				// loop through atches, and genearte the url.
				for ($i = 1; $i < count($matches); $i++) {
					if ($i > 1)
						$fileName .= '/';
					$fileName .= $matches[$i];
				}
				// append .php into it.
				$fileName .= ".php";
				// If file doesn't exist, find new match.
				if (!file_exists($fileName)) {
					$fileName = '';
					continue;
				}
				break;
			}
		}
		return array($fileName, $firstMatch);
	}

	public static function getRedirectType($type) {
		global $titleCond, $semiURL, $login;
		switch($type) {
			case 'student':
				$titleCond = "`title`='1'";
				$semiURL = 'STUDENT';
				break;
			case 'teacher':
				$titleCond = "`title`='2'";
				$semiURL = 'TEACHER';
				break;
			case 'staff':
				$titleCond = "`title`='2' OR `title`='4'";
				$semiURL = 'STAFF';
				break;
			case 'all':
				$titleCond = "`title`>'0'";
				$semiURL = 'ALL';
				break;
			case 'studentValidate':
				$titleCond = "`title`='1' AND `semester`='Sem0' AND `verified`='1'";
				$semiURL = "STUDENT_VERIFY";
				break;
			default:
				throw new Exception("$type ...");
				$login->addError("Invalid Title Specified");
				$login->redirect("CMSYS_PROFILE");
				break;
		}
		return;
	}
}
?>