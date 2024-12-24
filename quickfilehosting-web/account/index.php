<?php
session_start();
if (isset($_SESSION['login']) && !empty($_SESSION['login']) && isset($_SESSION['hash']) && !empty($_SESSION['hash'])) header("location: profile.php");
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
				<br><input class="logsignt" type="text" name="login" placeholder="Login" value="<?php if(isset($_SESSION['tmplogin'])) echo $_SESSION['tmplogin']; ?>"><br><br>
				<input class="logsignt" type="password" name="pass" placeholder="Password"><br><br>
				<input type="hidden" name="mode" value="login">
				<input class="logsign" type="submit" name="subbutton" value="Login">
				</form>
				<span class="text">Dont have account?  <a href="register.php">Register</a>.</span><br>
				<span class="text">Didn't got activation email?  <a href="resend.php">Resend</a>.</span><br>
				<span class="text">Forgot Password? <a href="recoverpass.php">Recover</a>.</span>
			</div></center>
		</div>
	</body>
</html>
<?php
unset($_SESSION['tmplogin']);
?>