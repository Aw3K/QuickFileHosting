<?php
session_start();
require_once "../config.php";

?>
<html>
	<head>
		<title>Quick File Hosting - Resend</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="/data/css/style.css">
		<link rel="icon" href="../data/images/icon.png">
		<link rel="stylesheet" type="text/css" href="/data/css/styleaccounts.css">
	</head>
	<body>
		<?php include "../data/php/header.html"; ?>
		<div class="container">
<?php

if (isset($_POST['email']) && !empty($_POST['email'])) {
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$res = $conn -> query("SELECT * FROM `users` WHERE `email` = '$email'");
	$out = $res->fetch_assoc();
	if ($out) {
		if ($out['active'] == false) {
			$login = $out['username'];
			$data = $out['createdate'];
			$hash = $out['hash'];
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
			<p class='text'>An account was created at quickfilehosting using that e-mail address.</p>
			<p class='text'>If that wasn't You, ignore that message.</p><br>
			<p class='text'>Account information:</p>
			<p class='text'>Username: ". $login .",</p>
			<p class='text'>create date: ". date("d.m.y, H:i:s", $data) .".</p><br>
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
			echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid green; width: 500px; font-size: 125%; border-radius: 5px;'> Verification email successfully resend. </div>";
		} else {
			echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid red; width: 500px; font-size: 125%; border-radius: 5px;'> Account already active. </div>";
		}
	} else {
		echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid red; width: 500px; font-size: 125%; border-radius: 5px;'> Account with that email don't exists. </div>";
	}
} else {
?>
			<center><div style="border: 4px solid green; max-width: 250px; margin: 10px; border-radius: 25px; background-color: rgba(0,0,0,.2);"><form method="post" action="">
				<br><input class="logsignt" type="text" name="email" placeholder="Type email there."><br><br>
				<input class="logsign" type="submit" name="subbutton" value="Resend">
				</form>
				<span class="text">Dont have account?  <a href="register.php">Register</a>.</span><br>
				<span class="text">Didnt got activation email?  <a href="resend.php">Resend</a>.</span><br>
				<span class="text">Forgot Password? <a href="recoverpass.php">Recover</a>.</span>
				</div></center>
<?php } ?>
		</div>
	</body>
</html>