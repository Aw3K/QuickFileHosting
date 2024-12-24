<?php
session_start();
include "../config.php";
if (!isset($_SESSION['login']) && empty($_SESSION['login']) && !isset($_SESSION['hash']) && empty($_SESSION['hash']) && !isset($_SESSION['id']) && empty($_SESSION['id'])) exit ("User isn't logged in.");
$mode = strip_tags($_GET['mode']);
if (!empty($mode) and isset($mode)) {
	$fid = strip_tags($_GET['fileid']);
	if (empty($fid)) {
		if ($mode != "RECA" && $mode != "DELA") exit ("That file dont belong to current user.");
	} else {
		$res = $conn -> query("SELECT * FROM `files` WHERE `id` = '$fid'");
		$data = mysqli_fetch_array($res);
		if ($data['owner'] != $_SESSION['id'] && $mode != "RECA") exit ("That file dont belong to current user.");
	}
	if ($mode == "DEL") {
		$action = $conn -> query("UPDATE `files` SET `remove` = 'true',`locked` = 'true' WHERE `id` = '$fid'");
		if ($action) exit ("SUCCESS");
		else exit ("Could not delete file. Try to reload page or contact support.");
	}
	else if ($mode == "DELA") {
		$action = $conn -> query("UPDATE `files` SET `remove` = 'true',`locked` = 'true' WHERE `owner` = '" . $_SESSION['id'] . "' AND `remove` = 'false'");
		if ($action) exit ("SUCCESS");
		else exit ("Could not delete file. Try to reload page or contact support.");
	}
	else if ($mode == "REN") {
		$name = strip_tags($_GET['name']);
		$res = $conn -> query("SELECT * FROM `files` WHERE `id` = '$fid'");
		$data = $res->fetch_assoc();
		$oldname = $data['fname'];
		$hash = $data['hash'];
		
		$oldss = "../files/data/".$hash."/".$oldname;
		$ext = pathinfo($oldss, PATHINFO_EXTENSION);
		$newss = "../files/data/".$hash."/".$name.".".$ext;
		
		$namee = $name.".".$ext;
		if (rename($oldss, $newss)) {
			$action = $conn -> query("UPDATE `files` SET `fname` = '$namee' WHERE `id` = '$fid'");
			if ($action) exit ("SUCCESS");
			else {
				rename($newss, $oldss);
				exit ("Could not rename file in database. Try to reload page or contact support.");
			}
		} else exit ("Couldn't rename file. Try to reload page or contact support.");
	}
	else if ($mode == "PASS") {
		$pass = mysqli_real_escape_string($conn, strip_tags($_GET['pass']));
		$pass = md5($pass);
		$action = $conn -> query("UPDATE `files` SET `pass` = '$pass' WHERE `id` = '$fid'");
		if ($action) exit ("SUCCESS");
		else exit ("Couldn't set password. Try to reload page or contact support.");
	}
	else if ($mode == "PASSOUT") {
		$action = $conn -> query("UPDATE `files` SET `pass` = '' WHERE `id` = '$fid'");
		if ($action) exit ("SUCCESS");
		else exit ("Couldn't remove password. Try to reload page or contact support.");
	}
	else if ($mode == "REC") {
		$action = $conn -> query("UPDATE `files` SET `remove` = 'false',`locked` = 'false' WHERE `id` = '$fid' AND `remove` = 'true'");
		if ($action) exit ("SUCCESSR");
		else exit ("Couldn't recover file. Try to reload page or contact support.");
	}
	else if ($mode == "RECA") {
		$action = $conn -> query("UPDATE `files` SET `remove` = 'false',`locked` = 'false' WHERE `owner` = '" . $_SESSION['id'] . "' AND `remove` = 'true'");
		if ($action) exit ("SUCCESSR");
		else exit ("Couldn't recover all files possible. Try to reload page or contact support.");
	}
	else exit ("Wrong parameters used for that request");
	
} else exit ("Wrong parameters used for that request");
?>