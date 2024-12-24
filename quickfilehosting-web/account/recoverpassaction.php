<?php
session_start();
require_once "../config.php";

?>
<html>
	<head>
		<title>Quick File Hosting - Password Recovery</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="/data/css/style.css">
		<link rel="stylesheet" type="text/css" href="/data/css/styleaccounts.css">
	</head>
	<body>
		<?php include "../data/php/header.html"; ?>
		<div class="container">
<?php
if (isset($recoverhash) && !empty($recoverhash)) {
	$recoverhash = mysqli_real_escape_string($conn, strip_tags($_GET['recoverhash']));
	$res = $conn-> query("SELECT * FROM `users` WHERE `passres` = '$recoverhash'");
	$data = $res->fetch_assoc();
	if (!empty($data['passres'])) {
		$_SESSION['PASSRESSTOKEN'] = $recoverhash;
	?>
			<center><div style="border: 4px solid green; max-width: 250px; margin: 10px; border-radius: 25px; background-color: rgba(0,0,0,.2);"><form method="post" action="revpass.php">
				<br><input class="logsignt" type="password" name="pass1" placeholder="Password"><br><br>
				<input class="logsignt" type="password" name="pass2" placeholder="Retry Password"><br><br>
				<input class="logsign" type="submit" name="subbutton" value="Change Password">
				</form>
			</div></center>
	<?php
	exit;
	} else echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid red; width: 500px; font-size: 125%; border-radius: 5px;'> Recoverhash expired or account dont exist. </div>";
} else echo "<div class='text' style='text-align: center; margin: 50px auto; border: 5px solid red; width: 500px; font-size: 125%; border-radius: 5px;'> Recoverhash not set. </div>";
?>
		</div>
	</body>
</html>