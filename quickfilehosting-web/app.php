<?php
session_start();
error_reporting(0);
require_once "./config.php";
?>
<html>
	<head>
		<title>Quick File Hosting - Application</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="data/css/style.css">
		<link rel="icon" href="./data/images/icon.png">
		<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="https://malsup.github.io/jquery.form.js"></script>
		<script type="text/javascript" src="https://underscorejs.org/underscore-min.js"></script>
		<script type="text/javascript" src="data/scripts/scripts.js"></script>
	</head>
	<body>
		<?php include "./data/php/header.html"; ?>
		<div class="container" id="container">
			<table style="margin: 10px; text-align: center; width: 100%;">
				<tbody>
					<tr>
						<td>
							<p class="text"> Click button below to download zipped binaries of QuickFileHosting Application. </p>
						</td>
					<tr>
					<tr>
						<td>
							<p class="text"> To use it Unzip it using build in Windows archives or Winrar/7Zip. </p>
						</td>
					<tr>
					</tr>
						<td>
							<a href="/data/QuickFileHosting.zip"><input id="submitFile" type="submit" value="Download"></a>
						</td>
					<tr>
					</tr>
						<td>
							<p class="text"> Supports 64 bit Windows 10/11 </p>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="loggedin"><center><span class="text"><?php
			
			if (isset($_SESSION['login']) && !empty($_SESSION['login']) && isset($_SESSION['hash']) && !empty($_SESSION['hash'])) echo "Logged as: " . $_SESSION['login']." <a href='account/logout.php'>Logout</a></span></center>";
			else {
				echo "Sends files as: Guest, <a href='account/index.php'>login</a> for more possiblities!</span></center>";
			}
			
			?></div>
		</div>
	</body>
</html>