<?php
session_start();
if (!isset($_SESSION['login']) && empty($_SESSION['login']) && !isset($_SESSION['hash']) && empty($_SESSION['hash']) && !isset($_SESSION['id']) && empty($_SESSION['id'])) header("location: index.php");
require_once "../config.php";

?>
<html>
	<head>
		<title>Quick File Hosting - Profile</title>
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
		<div class="container" style="margin-top: 40px;">
			<center><div class="selectpage"><p id="pt1" class="text pageselectbutton" onclick="pageState('LFILES')">LIST OWNED FILES</p><p class="text" style="font-size: 120%;">I</p><p id="pt2" class="text pageselectbutton" onclick="pageState('RFILES')">RECOVER FILES</p></div></center>
			<div id="filelist" class="filelist">
			</div>
			<center><div class="loggedin"><span class="text"><?php echo "Logged as: " . $_SESSION['login'] . ". "; ?><a href="logout.php">Logout</a></span></div></center>
		</div>
		<script>$('#filelist').load("profilefilelist.php");</script>
	</body>
</html>