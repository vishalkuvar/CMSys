<?php
	/**
	 * List Member Page
	 */
	// Import CSS
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/table.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/dropdown.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/account/input2.css">';
	echo '<script type="text/javascript" src="'.$rootDir.'/js/dropdown.js"></script>';
	// Show Error
	$count = ErrorHandler::showError();

	// If No error, then proceed with showing tables.
	if (!$count) {
		// There should be 6th deep URL.
		if (count($matches) != 6) {
			if (count($matches) == 5) {
				RedirectHandler::getRedirectType($matches[4]);
				RedirectHandler::redirect('CMSYS_LIST_'. $semiURL, 1);
			} else {
				$login->addError("Invalid URL Entered.");
				RedirectHandler::redirect('CMSYS_PROFILE');
			}
		}
		// Check the type
		$type = $matches[4];
		RedirectHandler::getRedirectType($type);
		$redirectURL = 'CMSYS_LIST_'. $semiURL;
		ErrorHandler::showInfo();
		// CurrentPage and Total Page.
		$currentPage = intval($matches[5]);
		$page = max(0, $currentPage-1);
		$i = 0;

		$searchCond = '';
		$empty = 0;
		// Checks if Cookie for search is set(name/branch/sem) and save them into session.
		if (isset($_COOKIE["sname"]))
			$_SESSION["sname"] = $_COOKIE["sname"];;
		if (isset($_COOKIE["sbranch"]))
			$_SESSION["sbranch"] = $_COOKIE["sbranch"];
		if (isset($_COOKIE["ssem"]))
			$_SESSION["ssem"] = $_COOKIE["ssem"];
		// If Session is set, convert them into Search Conditions for SQL.
		if (isset($_SESSION["sname"]) && !empty($_SESSION["sname"])) {
			$searchCond .= "`name` LIKE '%". $_SESSION["sname"] ."%' ";
			$empty = 1;
		}
		if (isset($_SESSION["sbranch"]) && !empty($_SESSION["sbranch"])) {
			if ($empty)
				$searchCond .= "AND ";
			$searchCond .= "`branch` LIKE '%". $_SESSION["sbranch"] ."%' ";
			$empty = 1;
		}
		if (isset($_SESSION["ssem"]) && !empty($_SESSION["ssem"])) {
			if ($empty)
				$searchCond .= "AND ";
			$searchCond .= "`semester` LIKE '%". $_SESSION["ssem"] ."%' ";
			$empty = 1;
		}
		// Atleast 1 session is set.
		if ($empty > 0)
			$searchCond = "AND ". $searchCond;
		// Execute Page Query.
		$members = $login->DB->pageQuery(array("SELECT * FROM `login` WHERE $titleCond $searchCond", "SELECT COUNT(*) FROM `login` WHERE $titleCond $searchCond"), $page);
		// Save Total Entries and Total Pages.
		$totalEntries = intval($members[1][0][0]);
		$totalPages = ceil($totalEntries/10);
		// If page is out of limit, guess the correct page and redirect.
		if ($page >= 1 && ($members[0] == NULL || $members[0]->num_rows == 0)) {
			if (!isset($step)) 
				$step = 10;
			$page = ceil($totalEntries/$step);	// Guess the Page
			RedirectHandler::redirect($redirectURL, $page);
		}

		// Search Bar
		$form_name = isset($_SESSION["sname"])?": ". $_SESSION["sname"]: "";
		$form_sem = isset($_SESSION["ssem"])?": ". $_SESSION["ssem"]: "";
		$form_branch = isset($_SESSION["sbranch"])?": ". $_SESSION["sbranch"]: "";
?>
	<!-- Search Bar -->
	<form onsubmit="return validateSearch();" action="<?php echo RedirectHandler::getRedirectURL($redirectURL, $i); ?>">
		Search:
		<input type="text" class="cmsys-fill cmsys-input2" placeholder="Name<?php echo $form_name; ?>" id="sname">
		<input type="text" class="cmsys-fill cmsys-input2" placeholder="Branch<?php echo $form_sem; ?>" id="sbranch">
		<input type="text" class="cmsys-fill cmsys-input2" placeholder="Sem<?php echo $form_branch; ?>" id="ssem">
		<input type="submit" class="cmsys-fill cmsys-button2">
	</form>
	<!-- Table Start -->
	<table class="cmsys-table" style="overflow-y: visible;">
		<!-- Table Headding -->
		<tr>
			<th>Sr.No</th>
			<th>UserID</th>
			<th>Name</th>
			<th>username</th>
			<th>Email</th>
			<th>Branch</th>
			<th>Sem</th>
			<th>IP</th>
			<th>Action</th>
		</tr>
<?php
		// Fetch SQL row by row.
		while (($res = $members[0]->fetch_assoc()) != NULL) {
			if ($i%2 == 0)
				echo "<tr>";
			else
				echo '<tr class="odd">';
			$i++;
			// Display Neccessary Details.
			echo "<td>$i</td>";
			echo '<td>'. $res['id'] .'</td>';
			echo '<td>'. $res['name'] .'</td>';
			echo '<td>'. $res['user'] .'</td>';
			echo '<td>'. $res['email'] .'</td>';
			echo '<td>'. $res['branch'] .'</td>';
			echo '<td>'. $res['semester'] .'</td>';
			echo '<td>'. $res['ip'] .'</td>';
			if ($semiURL != "STUDENT_VERIFY") {
?>
			<td>
				<!-- Dropdown for Actions -->
				<center>
					<div class="dropdown">
						<button class="dropbtn">Action</button>
						<div class="dropdown-content">
							<a href="<?php echo RedirectHandler::getRedirectURL('CMSYS_VIEW_'. $semiURL, $res['id']); ?>">View</a>
							<a href="<?php echo RedirectHandler::getRedirectURL('CMSYS_EDIT_'. $semiURL, $res['id']); ?>">Edit</a>
<?php
						if ($login->roleValid('delete_student'))
							echo '<a class="delete" nameJ="'. $res["name"] .'" idJ="'. $res["id"] .'" titleJ="'. RoleHandler::getTitleName($res['title']) .'" href="'. RedirectHandler::getRedirectURL('CMSYS_SYSTEM_DELETE_'. $semiURL, $res['id']) .'">Delete</a>';
?>
						</div>
					</div>
				</center>
			</td>
<?php
			} else {
?>
				<td>
					<a href="<?php echo RedirectHandler::getRedirectURL('CMSYS_SYSTEM_'. $semiURL, $res['id']) ?>">
						<button class="cmsys-fill cmsys-button2">Validate</button>
					</a>
				</td>
<?php
			}
			echo '</tr>';
		}
?>
</table>
<div style="margin: 5px; right: 30px; position: absolute">
<table cellpadding="2">
	<tr style="display: table-cell;">
	<?php
		// Next Page Generator.
		$start = max(1, min($totalPages, $currentPage-2));
		$end = min($totalPages, $currentPage+2);
		if ($currentPage == $totalPages) {
			$end = $currentPage;
		} else if ($currentPage == 1) {
			$start = $currentPage;
		}
		for ($i = $start; $i <= $end; $i++) {
			echo '<th><a href="'. RedirectHandler::getRedirectURL($redirectURL, $i) .'">'. $i .'</th>';
		}
	?>
	</tr>
</table>
</div>
<script type="text/javascript">
	/**
	 * OnClick Listener for delete button.
	 * Shows a Confirmation Box
	 */
	 $(".delete").click(function (e) {
 		var result = deleteConfirm($(this).attr('nameJ'), $(this).attr('idJ'), $(this).attr('titleJ'));
        if (result == false) {
            e.preventDefault();
        };
    });

	function deleteConfirm(name, id, title) {
		var message = 'Are you sure you want to delete the Following user: \n' +
						'Name: '+ name + '\n' +
						'Id: '+ id + '\n' +
						'Title: ' + title + '\n' +
						'Note: THE ACTION IS IRREVERSIBLE, PLEASE THINK BEFORE PERFORMING ANY ACTION';

		var result = window.confirm(message);
		return result;
	}

	/**
	 * Sets the cookie when search button is pressed.
	 * @method validateSearch
	 */
	function validateSearch() {
		var sname = document.getElementById("sname");
		var sbranch = document.getElementById("sbranch");
		var ssem = document.getElementById("ssem");
		document.cookie = "sname="+ sname.value;
		document.cookie = "sbranch="+ sbranch.value;
		document.cookie = "ssem="+ ssem.value;
		return true;
	}
</script>
<?php
	echo "<br/>";
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	}
?>