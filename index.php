<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<link rel="icon" href="favicon.ico" type="image/png" sizes="16x16">
<?php
	// Include Configurations and Modules
	require_once 'include.php';
	// Include Required CSS
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/css.css">';
	echo '<link rel="stylesheet" type="text/css" href="'. $rootDir .'/css/login.css">';
?>
<style>
body{
<?php
	// Multiple Background Images from 08:00 - 19:59; 20:00 - 07:59;
	if (date('H') >= 8 && date('H') < 20) {
		echo "background: url($rootDir/bg/library.png) no-repeat center center fixed;";
	} else {
		echo "background: url($rootDir/bg/back.png) no-repeat center center fixed;";
	}
?>
	background-color: #444;
	background-size: cover;
}

</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script>
	$(document).ready(function() {
		$('.formc').click(function(){
			$('form').animate({height: "toggle", opacity: "toggle"}, "slow");
			$('.cmsys-error-trans').toggle();
		});
	});
</script>
</head>
<body style="overflow-y: hidden">
<?php
	// If There's no error/info session, then check if SESSION for login is present.
	$e = new ErrorHandler();
	$e->startSession();
	if (empty($_SESSION['error']) && empty($_SESSION['info'])) {
		include dirname(__FILE__).'/cms/session.php';
		// If Login Session is present, redirect to Profile
		if ($sessionError == false && $isLoginSet == true) {
			RedirectHandler::redirect('CMSYS_PROFILE');
		}
	}
?>
	<div class="login-page">
		<div class="form">
		<span><img src="/cmsys/bg/BooksL.png" width="20%"  style="margin-bottom: 10px;"></span>
		<span><img src="/cmsys/bg/ParksText.png" width="50%" style="margin-bottom: 10px;"></span>
<?php
	// Show Register Page
	if (isset($_GET['register'])){
?>
		<script type="text/javascript">
			$(function() {
				$('form').toggle();
			})
		</script>
<?php
	}
	ErrorHandler::showError(1);
	ErrorHandler::showInfo(1);
?>
			<form class="register-form" method="post" action="<?php echo RedirectHandler::getRedirectURL('CMSYS_SYSTEM_REGISTER'); ?>">
				<input type="text" placeholder="Name" name="name"/>
				<input type="text" placeholder="Username" name="username"/>
				<input type="password" placeholder="Password" name="password"/>
				<input type="password" placeholder="Confirm Password" name="password_confirmed"/>
				<input type="text" placeholder="Email Address" name="email"/>
				<button>Create</button>
				<p class="message">Already registered? <a href="#" class="formc">Sign In</a></p>
			</form>
			<form class="login-form" method="post" action="<?php echo RedirectHandler::getRedirectURL('CMSYS_SYSTEM_LOGIN'); ?>">
				<input type="text" placeholder="username" name="username"/>
				<input type="password" placeholder="password" name="password"/>
				<button>Login</button>
				<p class="message">Not registered? <a href="#" class="formc">Create an account</a></p>
			</form>
		</div>
	</div>
</body>
</html>
