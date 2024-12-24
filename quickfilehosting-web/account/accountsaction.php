<?php
session_start();
include "../config.php";

?>
<html>
	<head>
		<title>Quick File Hosting - Accounts</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="/data/css/style.css">
		<link rel="stylesheet" type="text/css" href="/data/css/styleaccounts.css">
		<link rel="icon" href="../data/images/icon.png">
		<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="https://underscorejs.org/underscore-min.js"></script>
		<script type="text/javascript" src="../data/scripts/scriptsprofile.js"></script>
	</head>
	<body>
		<?php include "../data/php/header.html"; ?>
		<div class="container">
<?php

if (isset($_POST['mode']) && !empty($_POST['mode'])) { 
	if ($_POST['mode'] == "login") 
	{
		if (isset($_POST['login']) && !empty($_POST['login']) && isset($_POST['pass']) && !empty($_POST['pass'])) {
			$login = mysqli_real_escape_string($conn, $_POST['login']);
			$pass = mysqli_real_escape_string($conn, $_POST['pass']);
			$_SESSION['tmplogin'] = $login;
			$res = $conn -> query("SELECT * FROM `users` WHERE `username` = '$login'");
			$out = $res->fetch_assoc();
			if ($out && sodium_crypto_pwhash_str_verify($out['password'], $pass) == 0) {
				if ($out['active'] == true) {
					$_SESSION['login'] = $out['username'];
					$_SESSION['id'] = $out['id'];
					$_SESSION['hash'] = $out['hash'];
					unset($_SESSION['tmplogin'], $_SESSION['tmpemail']);
					echo "<meta http-equiv='refresh' content='0; url=profile.php' />";
				} else {
					echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid red; width: 500px; font-size: 125%; border-radius: 5px;'> User account isn't activated yet, check email. Click <a href='resend.php'> there </a> if need to reset activation email. </div>";
				}
			} else {
				echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid red; width: 500px; font-size: 125%; border-radius: 5px;'> User at typed login and password dont exist. Wanna make new account? Click <a href='register.php'> there</a>. </div>";
			}
		} else {
			echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid red; width: 500px; font-size: 125%; border-radius: 5px;'> Fill all fields before continue. </div>";
		}
	} else if ($_POST['mode'] == "register") {
		if (isset($_POST['login']) && !empty($_POST['login']) && isset($_POST['pass']) && !empty($_POST['pass']) && isset($_POST['pass2']) && !empty($_POST['pass2']) && isset($_POST['email']) && !empty($_POST['email'])) {
			$login = mysqli_real_escape_string($conn, $_POST['login']);
			if (strlen($login) > 32) $login = substr($login, 0, 32);
			$pass = mysqli_real_escape_string($conn, $_POST['pass']);
			$pass2 = mysqli_real_escape_string($conn, $_POST['pass2']);
			$email = mysqli_real_escape_string($conn, $_POST['email']);
			$_SESSION['tmplogin'] = $login;
			$_SESSION['tmpemail'] = $email;
			$errors = array();
			if (strlen($login) < 6) $errors[] = "Login can't be shorter than 6 characters.";
			if ($pass != $pass2) $errors[] = "Passwords don't match.";
			if (strlen($pass) < 8) $errors[] = "Password can't be shorter than 8 characters.";
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email is invalid.";
			$res = $conn -> query("SELECT * FROM `users` WHERE `username` = '$login'");
			if (mysqli_fetch_row($res) > 0) $errors[] = "Login is already in use, pick new one.";
			$res = $conn -> query("SELECT * FROM `users` WHERE `email` = '$email'");
			if (mysqli_fetch_row($res) > 0) $errors[] = "Email is already in use, pick new one.";
			
			if (empty($errors)) {
				$pass = sodium_crypto_pwhash_str(
					$pass,
					SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
					SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
				);
				$hash = uniqid(generateRandomString(), true);
				$data = date("d.m.y, H:i:s");
				$conn -> query("INSERT INTO `users` (`username`, `password`, `email`, `hash`, `createdate`, `active`) VALUES ('$login', '$pass', '$email', '$hash', ".time().", '0')");
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= "From: quickfilehosting@gmail.com\r\n"."X-Mailer: php";
				$subject = "QuickFileHosting - account activation";
				$message = "<html>
	<head>
		<style>
			.text {
				color: green;
				margin: 5px;
				font-weight: bold;
			}
			.container {
				width: 596px;
				border: 2px solid green;
				background-color: #191d21;
				text-align: left;
			}
		</style>
	</head>
	<body>
		<center><img src='https://quickfilehosting.ddns.net/data/images/logo.png'>
		<div class='container'>
			<p class='text'>An account was created at quickfilehosting.ddns.net using that e-mail address.</p>
			<p class='text'>If that wasn't You, ignore that message.</p><br>
			<p class='text'>Account information:</p>
			<p class='text'>Username: ". $login .",</p>
			<p class='text'>create date: ". $data .".</p><br>
			<p class='text'>To activate account click in link below.</p>
			<p class='text'><a href='https://quickfilehosting.ddns.net/account/activate.php?email=". $email ."&hash=". $hash ."'>https://quickfilehosting.ddns.net/account/activate.php?email=". $email ."&hash=". $hash ."</a></p><br>
			<p class='text'>Thanks for using our page!</p>
			<p class='text'>QuickFileHosting Team</p><br>
			<p class='text'>This message was generated automaticaly, please do not responde.</p>
		</div></center>
	</body>
</html>";
				mail($email,$subject,$message,$headers);
				Sleep(2);
				echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid green; width: 500px; font-size: 125%; border-radius: 5px;'> You have been succesflly registered. Check email for a link to activate account. </div><meta http-equiv='refresh' content='5; url=index.php' />";
				unset($_SESSION['tmplogin'], $_SESSION['tmpemail']);
			} else {
				echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid red; width: 500px; font-size: 125%; border-radius: 5px;'> Fix isses before continue: <ul>";
				foreach($errors as $out) {
					echo "<li>$out</li>";
				}
				echo "</ul></div>";
			}
		} else {
			echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid red; width: 500px; font-size: 125%; border-radius: 5px;'> Fill all fields before continue. </div>";
		}
	} else {
		echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid red; width: 500px; font-size: 125%; border-radius: 5px;'> No specific mode set, retry last action. </div>";
	}
} else {
	echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid red; width: 500px; font-size: 125%; border-radius: 5px;'> No post action set, retry last action. </div>";
}
?>
		</div>
	</body>
</html>