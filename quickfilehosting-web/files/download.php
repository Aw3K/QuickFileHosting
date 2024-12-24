<?php
error_reporting(0);
session_start();
require_once "./../config.php";

if (isset($_GET['token'])) $token = mysqli_real_escape_string($conn, htmlspecialchars($_GET['token']));
$mode = htmlspecialchars($_GET['mode']);
if (isset($token) && !empty($token)) {
	$res = $conn -> query ("SELECT * FROM `files` WHERE `token` = '$token'");
	if (mysqli_fetch_row($res) == 0){
		header("location: /");
	} else {
		$res = $conn -> query ("SELECT * FROM `files` WHERE `token` = '$token'");
		$data = mysqli_fetch_array($res);
		if (($_SESSION[$token]['AUTH'] != $token && !empty($data['pass'])) || $data['locked'] == "true") {
			header("location: /x/{$token}");
			exit();
		}
		$file = "./data/".$data['hash']."/".$data['fname'];
		if ($mode != "VIEW") {
			$count = $data['dcount'];
			$count++;
			$newex = new DateTime(date('Y-m-d'));
			$newex -> modify ('+1 month');
			$conn -> query ("UPDATE `files` SET `expired` = '" . $newex->format('Y-m-d') . "', `dcount` = '$count' WHERE `token` = '$token'");
			unset($_SESSION[$token]['AUTH']);
		}
		header("Content-Description: File Transfer"); 
		header("Content-Type: application/octet-stream"); 
		header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		readfile ($file);
		exit();
	}
} else {
	header("location: /");
}
?>