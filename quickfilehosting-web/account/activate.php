<?php
require "../config.php";

$email = strip_tags($_GET['email']);
$hash = strip_tags($_GET['hash']);
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
if (isset($email) && !empty($email) && isset($hash) and !empty($hash)) {
	$email = mysqli_real_escape_string($conn, $email);
	$hash = mysqli_real_escape_string($conn, $hash);
	$res = $conn -> query("SELECT * FROM `users` WHERE `email` = '$email' AND `hash` = '$hash'");
	if ($out = $res->fetch_assoc()) {
		if ($out['active'] == false) {
			$res = $conn -> query("UPDATE `users` SET `active` = '1' WHERE `email` = '$email' AND `hash` = '$hash'");
			if ($res) echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid green; width: 500px; font-size: 125%; border-radius: 5px;'> Account has been activated! You can now <a href='index.php'>login</a>. </div>";
			else echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid red; width: 500px; font-size: 125%; border-radius: 5px;'> There were an erorr activating account. Retry. </div>";
		} else {
			echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid red; width: 500px; font-size: 125%; border-radius: 5px;'> Account already active. </div>";
		}
	} else {
		echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid red; width: 500px; font-size: 125%; border-radius: 5px;'> Account don't exist. </div>";
	}
}
?>
		</div>
	</body>
</html>