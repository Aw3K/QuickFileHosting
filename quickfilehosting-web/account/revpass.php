<?php
session_start();
require_once "../config.php";
$pass1 = mysqli_real_escape_string($conn, $_POST['pass1']);
$pass2 = mysqli_real_escape_string($conn, $_POST['pass2']);
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
if ($pass1 != $pass2) echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid red; width: 500px; font-size: 125%; border-radius: 5px;'> Passwords dont match. </div>";
else if (strlen($pass1) < 8) echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid red; width: 500px; font-size: 125%; border-radius: 5px;'> Too short password. </div>";
else {
	$pass1 = sodium_crypto_pwhash_str(
		$pass1,
		SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
		SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
	);
	$conn -> query("UPDATE `users` SET `password` = '$pass1',`passres` = '' WHERE `passres` = '" . $_SESSION['PASSRESSTOKEN'] . "'");
	unset($_SESSION['PASSRESSTOKEN']);
	echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid green; width: 500px; font-size: 125%; border-radius: 5px;'> You have been succesflly changed password. Will be redirected in 3 secounds. </div><meta http-equiv='refresh' content='3; url=profile.php' />";
}
?>
		</div>
	</body>
</html>