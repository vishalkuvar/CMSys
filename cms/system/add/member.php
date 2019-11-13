
<?php
/**
 * Checks for Post Argument,
 * inserts data into global $data and $column if POST is not empty.
 * @method emptyCheck
 * @param  string     $var         POST Argument
 * @param  int     $addEmpty    value to be added in $empty
 * @param  string     $forceValue  (default: NULL), if provided, uses this instead of $_POST
 * @param  bool       $delayInsert (default:false), to insert $data of current var or to delay it.
 * @return string                  Contents of POST Argument.
 */
function emptyCheck($var, $addEmpty, $forceValue = null, $delayInsert = false)
{
    global $empty, $data, $column;
    $temp = null;
    if ($forceValue != null) {
        $_POST[$var] = $forceValue;
    }
    if (!empty($_POST[$var])) {
        $temp = $_POST[$var];
        if (!$delayInsert) {
            $data[] = $temp;
            $column[] = $var;
        }
        $empty |= $addEmpty;
    }
    return $temp;
}
// Check for URL
if (count($matches) != 5) {
    $login->addError("Invalid URL.");
    RedirectHandler::redirect('CMSYS_PROFILE');
}
// Get Type
$type = $matches[4];
RedirectHandler::getRedirectType($type);
$redirectURL = 'CMSYS_ADD_'. $semiURL;
$data = array();
$column = array();
$empty = 0;

// Gather all Useful Variables.
$user = emptyCheck('user', 1);
$name = emptyCheck('name', 2);
$email = emptyCheck('email', 4);
$title = emptyCheck('title', 8, null, true);
$branch = emptyCheck('branch', 16);
$semester = emptyCheck('semester', 32);
$password = emptyCheck('password', 32);

if ($empty == 0) {
    ErrorHandler::addError("No Details Provided to Update.");
}

// Check if User Already Exists
if (($empty&1) > 0 && $login->userExists($user)) {
    $login->addError("Please choose another username.");
} elseif (($empty&4) > 0 && !$login->isValidEmail($email)) {    // Email should be in proper format.
    $login->addError("Please Enter Valid Email(". ($empty&4) .").");
} elseif (($empty&8) > 0 && !($title = RoleHandler::getTitleId($title))) {    // Valid Title should be given.
    $login->addError("Please Enter Valid Title");
} elseif (($empty&16) > 0 && !$login->isValidBranch($branch)) {    // Branch should be valid.
    $login->addError("Please Enter Valid Branch");
} elseif (($empty&32) > 0 && !$login->isValidSem($semester)) {    // Semester should be valid.
    $login->addError("Please Enter Valid Semester");
}
if ($title != 1 && $login->title == 4) {  // Staff can add student's only
    $login->logs->insertLogByType(CMSYS_LOG_ERROR, array("reasonb" => "Tried to Add '$user' with Title: '$title'"));
    $login->addError("You can only add Students.");
}
if ($semiURL == "STUDENT" && $title != 1) {
    $login->addError("Cannot Change Title of User");
}
// Insert TitleID
emptyCheck('title', 8, $title);
$login->errorRedirect($redirectURL);
// Update the User with Details submitted.
$userId = $login->register($user, $password, $email, $name, false);
$login->updateTable($column, $data, $userId, false);
$login->addInfo('Member Added Successfully');
// Log
$login->logs->insertLogByType(CMSYS_LOG_ADD_MEMBER, array("reasona" => "UserName: $user, Name: $name, Email: $email, Title: ". RoleHandler::getTitleName($title) .", Semester: $semester, Branch: $branch"));
$login->redirect($redirectURL);
?>
