<?php
session_start();
error_reporting(0);
require "../config.php";
if (!isset($_SESSION['login']) && empty($_SESSION['login']) && !isset($_SESSION['hash']) && empty($_SESSION['hash']) && !isset($_SESSION['id']) && empty($_SESSION['id'])) die ('USER NOT LOGGED');
$res = $conn -> query("SELECT * FROM `files` WHERE `owner` = '" . $_SESSION['id'] . "' AND `remove` = 'false'");
if ($res->num_rows) {
	$rallout = true;
	while($data = $res->fetch_assoc()) {
		if ($data['size'] == 0) {
			$size = "SIZE: UNKNOWN";
		} else {
			$size = number_format($data['size']/1048576, 3, '.', '');
			$size .= "MB";
		}
			echo "<div class='file'>
				<div class='fileintel' style='position: relative;'><span class='text'>" .$data['fname']. " | " .$size. " | DOWNLOADED: " .$data['dcount']. "</span>";
				if ($rallout) {
					echo "<button class='filebutton' style='position: absolute; right: 10px;' onclick=\"fileManipulation('DELA', 0);\">DELETE ALL</button>";
					$rallout = false;
				}
			echo "</div>
				<div class='timeintel inline'><span class='text'>CREATED: " .$data['created']. " | EXPIRES: " .$data['expired']. " </span></div><div class='filesactions inline'>
					<button class='filebutton' onclick=\"openInNewTab('https://quickfilehosting.ddns.net/x/" .$data['token']. "');\">OPEN</button>
					<span style='float:right;' class='text'>|</span><button class='filebutton' onclick=\"fileManipulation('DEL', " .$data['id']. ");\">DELETE</button>
					<span style='float:right;' class='text'>|</span><button class='filebutton' onclick=\"fileManipulation('REN', " .$data['id']. ");\">RENAME</button>";
			if (empty($data['pass'])) echo "<span style='float:right;' class='text'>|</span><button class='filebutton' onclick=\"fileManipulation('PASS', " .$data['id']. ");\">SET PASS</button>";
			else echo "<span style='float:right;' class='text'>|</span><button class='filebutton' onclick=\"fileManipulation('PASSOUT', " .$data['id']. ");\">REMOVE PASS</button>";
					
			echo   "<span style='color: green; float: right;'>ACTIONS:</span>
					<div style='clear: both'></div>
				</div>
				<div style='clear: both'></div>
			</div>";
	}
} else {
	echo "<div style='width: 100%; border:1 px solid green; border-radius: 5px; height: 30px; line-height: 30px;'><span class='text'><center>You don't have any files yet, upload some!</center></span></div>";
}
?>