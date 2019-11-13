<?php
$empty = 0;	// Fields provided by POST
$user = '';	// Default User
// Column Name and Data Array Initialized.
$column = array();
$data = array();
$log = '';
// Check for username/name/email and store them into seperate variables.
if (!empty($_POST['username'])) {
	$user = $_POST['username']; $data[] = $user;
	$column[] = 'user';
	$empty |= 1;
	$log .= "User: $user, ";
}
if (!empty($_POST['name'])) {
	$name = $_POST['name']; $data[] = $name;
	$column[] = 'name';
	$empty |= 2;
	$log .= "Name: $name, ";
}
if (!empty($_POST['email'])) {
	$email = $_POST['email']; $data[] = $email;
	$column[] = 'email';
	$empty |= 4;
	$log .= "Email: $email, ";
}

// No Details Provided.
if ($empty == 0) {
	ErrorHandler::addError("No Details Provided to Update.");
}

// Check if Same user already exists.
if (($empty&1) > 0 && $login->userExists($user)) {
	$login->addError("Please choose another username.");
} else if (($empty&4) > 0 && !$login->isValidEmail($email)) {	// Check if Email is entered in valid format.
	$login->addError("Please Enter Valid Email(". ($empty&4) .").");
}

$login->errorRedirect('CMSYS_UPDATE_PROFILE');
// Update the Table.
$login->updateTable($column, $data);
$login->addInfo('Details Updated Successfully');
// Insert Log of Update Profile
$login->logs->insertLogByType(CMSYS_LOG_UPDATE_DETAILS, array("reasona" => ".". $log));
$login->redirect('CMSYS_UPDATE_PROFILE');
?>