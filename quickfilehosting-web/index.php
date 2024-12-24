<?php
session_start();
error_reporting(0);
require_once "./config.php";
?>
<html>
	<head>
		<title>Quick File Hosting - File hosting up to 512MB</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="data/css/style.css">
		<link rel="icon" href="./data/images/icon.png">
		<script type='text/javascript' src='https://code.jquery.com/jquery-3.3.1.min.js'></script>
		<script type="text/javascript" src="https://malsup.github.io/jquery.form.js"></script>
		<script><?php echo "var maxsize = ".$SETTINGS['maxsize'] ?></script>
		<script type="text/javascript" src="data/scripts/scripts.js"></script>
	</head>
	<body>
		<?php include "./data/php/header.html"; ?>
		<div class="container">
			<center><p class="text">Click and choose a file to upload!</p></center>
			<form action="upload.php" id="myForm" name="frmupload" method="post" enctype="multipart/form-data">
				<center><div style="position: relative; display: inline-table;">
					<div class="upload_file">
						<input onclick="start()" type="file" id="upload_file" name="upload_file" autoComplete="off">
					</div>
					<div class="fakeupload">
						<input id="fakein" placeholder="Click there to select file">
					</div>
				</div>
				<input id="submitfile" type="submit" name='submit' value="Start Upload" onclick="upload_file_ajax(event);"><br>
				<div style="max-width: 350px; width: auto;"><font size="1"><p>*QuickFileHosting isn't the owner of uploaded files, we arent resposible of sources of uploaded files and their possible copyright.</p></font></div>
				</center>
			</form>
			<center><span id="out"></span>
			<div class='progress' id="progress_div">
				<div class='bar' id='bar1'></div>
				<div class='percent' id='percent1'>0%</div>
			</div></center>
			<table style="color: green;" align="center">
                <tbody>
					<tr>
						<td>
							<img src="data/images/tick.png" alt="">No Sign Up Required.<br>
							<img src="data/images/tick.png" alt="">File Life: 30 days after no activity.<br>
						</td>
						<td>
                            <img src="data/images/tick.png" alt="">Files are protected from unauthorised users.<br>
                            <img src="data/images/tick.png" alt="">No ridiculous queues.<br>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="loggedin"><center><span class="text"><?php
			
			if (isset($_SESSION['login']) && !empty($_SESSION['login']) && isset($_SESSION['hash']) && !empty($_SESSION['hash'])) echo "Logged as: " . $_SESSION['login']." <a href='account/logout.php'>Logout</a></span></center>";
			else {
				echo "Sends files as: Guest, <a href='account/index.php'>login</a> for more possibilities!</span></center>";
			}
			
			?></div>
		</div> 
	</body>
</html>