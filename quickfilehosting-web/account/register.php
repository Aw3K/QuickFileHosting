<?php
session_start();
require_once "../config.php";
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
		<script type="text/javascript" src="https://malsup.github.io/jquery.form.js"></script>
		<script type="text/javascript" src="https://underscorejs.org/underscore-min.js"></script>
		<script type="text/javascript" src="/data/scripts/scripts.js"></script>
	</head>
	<body>
		<?php include "../data/php/header.html"; ?>
		<div class="container">
			<center><div style="border: 4px solid green; max-width: 250px; margin: 10px; border-radius: 25px; background-color: rgba(0,0,0,.2);"><form method="post" action="accountsaction.php">
				<br><input class="logsignt" type="text" name="login" placeholder="Login" maxlength="32" value="<?php if(isset($_SESSION['tmplogin'])) echo $_SESSION['tmplogin']; ?>"><br><br>
				<input class="logsignt" type="password" name="pass" placeholder="Password"><br><br>
				<input class="logsignt" type="password" name="pass2" placeholder="Repeat Password"><br><br>
				<input class="logsignt" type="text" name="email" placeholder="E-mail" value="<?php if(isset($_SESSION['tmpemail'])) echo $_SESSION['tmpemail']; ?>"><br><br>
				<input type="hidden" name="mode" value="register">
				<input class="logsign" type="submit" name="subbutton" value="Register">
				</form><span class="text">Already have account? Login <a href="index.php">there</a>.</span>
				<span class="text">Didnt got activation email? Resend <a href="resend.php">there</a>.</span></div></center>
		</div>
	</body>
</html>
<?php
unset($_SESSION['tmplogin'], $_SESSION['tmpemail']);
?>