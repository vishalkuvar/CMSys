<?php
/**
 * Main Template Page
 * Every other page is included inside this.
 */
session_start();
require_once(dirname(__FILE__).'/../include.php');
include(dirname(__FILE__).'/session.php');
if ($sessionError == true || $isLoginSet == false) {
	$e = new ErrorHandler();
	$e->redirect('CMSYS_INDEX');
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Profile</title>
<?php
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/nav.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/css.css">';
	// <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	echo '<script type="text/javascript" src="'.$rootDir.'/js/jquery.min.js"></script>';
?>
<style>
#nav {
	background-color: #00FFFF;
}
#navright {
	position: relative;
    left: 950px;
}
#footer_image, #footer_park_image {
	display: none;
    position: absolute;
    width: 250px;
    height: 181px;
    /*
    border: 1px solid blue;
    */
}
#footer_text, #footer_park {
	z-index: 100;
	color: black;
	font-size: 12px;
	font-weight: bold;
	left: center;
}
</style>
</head>
<body>
	<div class="wrap">
	<div class="site-header">
		<img src="/cmsys/bg/BooksL.png" style="width: 5%; position: absolute; top: 6px; left: 20px;"></img>
		<img src="/cmsys/bg/ParksText.png" style="width: 10%; position: absolute; top: 10px; left: 100px;"></img>
		<header class="push">College Management System</header>
	</div>
		<?php
			if ($menuAbove)
				echo '<div class="menu-btn menu-header">&#9776; Menu</div>';
			// Get Login Class.
			$login = unserialize($_SESSION['login']);
			// Generate Navigation Menu
			$nav = new NavigationSubMenu();
			$nav->start();
			// Actions of Navigation Menu.
			$actions = array(
				array("Welcome ". $login->name, "#"),
				array("#", "#"),
				array("Home Page", "CMSYS_PROFILE", "view_profile"),
				array("My Account", array(
											array("Update Profile", "CMSYS_UPDATE_PROFILE", "update_profile"),
											array("View Logs", "CMSYS_LOGS", "view_logs"),
									)),
				array("Academics", array(
											array("View Attendance", "CMSYS_VIEW_ATTENDANCE", "view_attendance"),
											array("View Test Marks", "CMSYS_VIEW_MARKS", "view_test_marks"),
									)),
				array("Notice Board", array(
											array("View Notes", "CMSYS_VIEW_NOTES", "view_notes"),
											array("View Assignments", "CMSYS_VIEW_ASSIGNMENT", "view_assignment"),
											array("View Notices", "CMSYS_VIEW_NOTICE", "view_notice"),
									)),
				array("Examination", array(
											array("Add Exam Papers", "CMSYS_ADD_PAPER", "add_exam_paper"),
											array("List Exam Papers", "CMSYS_LIST_PAPER", "list_exam_paper"),
									)),
				array("Notify", array(
											array("List Uploaded Files", "CMSYS_LIST_FILES", "list_notes_notice_assign"),
											array("Add Notes", "CMSYS_ADD_NOTES", "add_notes"),
											array("Add Assignment", "CMSYS_ADD_ASSIGNMENT", "add_assignment"),
											array("Add Notice", "CMSYS_ADD_NOTICE", "add_notice"),
									)),
				array("Attendance", array(
											array("Add Attendance", "CMSYS_ADD_ATTENDANCE", "add_attendance"),
											array("List Attendance", "CMSYS_LIST_ATTENDANCE", "add_attendance"),
									)),
				array("Admin Control", array(
											array("List All Members", "CMSYS_LIST_ALL", "list_all"),
											array("#","#"),	// Seperator
											array("List Staff", "CMSYS_LIST_STAFF", "list_staff"),
											array("Add Staff", "CMSYS_ADD_STAFF", "add_staff"),
											array("#","#"),	// Seperator
											array("List Student", "CMSYS_LIST_STUDENT", "list_student"),
											array("Verify Student", "CMSYS_LIST_STUDENT_VERIFY", "validate_student"),
											array("Add Student", "CMSYS_ADD_STUDENT", "add_student"),
									)),
				array("Contact Staff", "CMSYS_CONTACT_STAFF", "contact_staff"),
				array("#", "#"),
				array("Logout", 'CMSYS_LOGOUT'),
			);
			// Generate Menu
			$nav->parseArray($actions);
			$nav->end();
		?>

		<!-- Site Overlay -->
		<div class="site-overlay"></div>
		<div id="container" style="overflow-y: hidden; height:auto !important">
			<?php
				if (!$menuAbove)
					echo '<div class="menu-btn">&#9776; Menu</div>';
				echo '<center><h1 id="page_heading"></h1></center>';
				// Get the requested url.
				$url = $_SERVER['REQUEST_URI'];
				// Remove '/cmsys/' from URL
				$subUrl = substr($url, 7);
				$passed = false;
				// Check All Redirect Indexes to find best possible match.
				for ($i = 0; $i < count($redirect_indexes); $i = $i+4) {
					$testUrl = $redirect_indexes[$i+1];
					$title = $redirect_indexes[$i+3];
					$testUrl = '/'.str_replace('/', '\/', $testUrl).'/';
					//print($testUrl.":".$subUrl.":". preg_match($testUrl, $subUrl) ."<br/>");
					// Match the String.
					if (preg_match($testUrl, $subUrl)) {
						$passed = true;
						$role = $redirect_indexes[$i+2];
						//print($role."<br/>");
						if ($role == '')
							break;
						// Check if User can perform the role.
						if ($login->roleValid($role)) {
							$passed = true;
							break;
						} else {	// User cannot perform the role, set passed to false.
							$passed = false;
						}
					}
				}
				if ($passed == false) {	// Invalid Page/Unauthorized
					include 'error404.php';
				} else {
					// Authorized, get the Template Name.
					$array = RedirectHandler::getIncludeName($url);
					$fileToInclude = $array[0];
					$matches = $array[1];
					// If File doesn't exist, show 404 page.
					if (!file_exists($fileToInclude)) {
						include 'error404.php';
					} else {
						// Include the Template
						include $fileToInclude;
					}
				}
			?>
		</div>
		<div id="push" style="height: 30px"></div>
	</div>
	<div id="container_footer" style="height: 30px;">
		<!-- Footer -->
		<footer>
		<center>
			<div>
				<img id="footer_image" src="/cmsys/bg/cmsys.png" width="300px" />
				<div id="footer_park_image">
					<img src="/cmsys/bg/parks.png" width="300px" />
					<span><font color="black" style="font-family: 'Times New Roman', Times, serif;">Dastgir <font color="#00FF00"><b>P</b></font>ojee, <font color="#00FF00"><b>A</b></font>bbas Meghani, Fahim <font color="#00FF00"><b>R</b></font>arh, Vishal <font color="#00FF00"><b>K</b></font>uvar, Farooq <font color="#00FF00"><b>S</b></font>heikh</font></span>
				<img id="footer_image" src="/cmsys/bg/cmsys.png" width="300px" />
			</div>
			<div id="container_logo">
				<p id="footer_text">
					<span id="footer_span">
						Powered By CMSys
					</span>
					<span id="footer_park">
						 (PARKS)
					</span>
				</p>
			</div>
		</center>
		</footer>
	</div>
</nav>
<script type="text/javascript">
	var timeout1, timeout2;
	function hoverCard(span_id, image_id, top, left) {
		pos = $(span_id).offset();
	    console.log(pos.top, pos.left-50);

	    var timeout = setTimeout(function() {
	        $(image_id).fadeIn().css({
	            'top': pos.top - top + 'px',
	            'left': pos.left - left + 'px',
	        });
	    }, 1000);
	    return timeout;
	};

	$('#footer_span').hover(function() {timeout1 = hoverCard('#footer_span', '#footer_image', 200, 40)}, function() {
	    clearTimeout(timeout1);
	});

	$('#footer_park').hover(function() {timeout2 = hoverCard('#footer_park', '#footer_park_image', 225, 80)}, function() {
	    clearTimeout(timeout2);
	});

	$('#footer_span').mouseleave(function() {
	    $('#footer_image').fadeOut();
	});

	$('#footer_park').mouseleave(function() {
	    $('#footer_park_image').fadeOut();
	});
	document.title = '<?php echo $title; ?>';
	document.getElementById('page_heading').innerHTML = '<?php echo $title; ?>';
</script>
<?php
	echo '<script src="'. $rootDir .'/js/nav.js"></script>';
?>
</body>
</html>
