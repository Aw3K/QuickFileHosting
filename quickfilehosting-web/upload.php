<?php
session_start();
require "./config.php";

function setMsgCode($msg){
	$_SESSION['errorcode'] = $msg;
	echo $msg;
}

function setErrorCode($err){
	$_SESSION['errorcode'] = $err;
	exit ($err);
}

if (!$conn) {setErrorCode("NOCONNECTED");}
else {
	if(isset($_POST['submit']))
	{
		$random = generateRandomString(10);
		$hash = md5(generateRandomString(20));
		if ($_FILES['upload_file']['size'] > $SETTINGS['maxsize']) {setErrorCode("BIG");}
		else if (strlen($_FILES['upload_file']['name']) == 0) {setErrorCode("NOSELECTED");}
		else {
			$datee = new DateTime(date('Y-m-d'));
			$exdate = new DateTime(date('Y-m-d'));
			$exdate -> modify ('+1 month');
			if (!$conn) {setErrorCode("NOCONNECTED");}
			else {
				$uploadfile=$_FILES["upload_file"]["tmp_name"];
				mkdir("./x/".$random);
				mkdir("./files/data/".$hash);
				$folder="./files/data/".$hash."/";
				if ($_FILES["upload_file"]["error"] == UPLOAD_ERR_OK) {
					if (move_uploaded_file($_FILES["upload_file"]["tmp_name"], $folder.$_FILES["upload_file"]["name"])) {
						if (!isset($_SESSION['login']) && empty($_SESSION['login']) && !isset($_SESSION['id']) && empty($_SESSION['id'])) {
							$conn -> query("INSERT INTO `files` (`token`, `hash`, `created`, `fname`, `size`, `expired`, `userip`, `remove`) VALUES ('$random', '$hash', '".$datee->format('Y-m-d')."', '".$_FILES['upload_file']['name']."', " . $_FILES['upload_file']['size'] . ", '".$exdate->format('Y-m-d')."', '" . $_SERVER['REMOTE_ADDR'] . "', 'false')");
							setMsgCode("SEND51");
						} else {
							$res = $conn -> query("SELECT * FROM `users` WHERE `id` = '" . $_SESSION['id'] . "'");
							$userdata = mysqli_fetch_array($res);
							if (!$userdata) $userdata['id'] = 0;
							$conn -> query("INSERT INTO `files` (`token`, `hash`, `created`, `fname`, `size`, `expired`, `userip`, `remove`, `owner`) VALUES ('$random', '$hash', '".$datee->format('Y-m-d')."', '".$_FILES['upload_file']['name']."', '".$_FILES['upload_file']['size']."', '".$exdate->format('Y-m-d')."', '".$_SERVER['REMOTE_ADDR']."', 'false', ".$userdata['id'].")");
							setMsgCode("SEND52");
						}
					} else {
						setErrorCode("NOMOVE");
					}
				} else {
					setErrorCode($_FILES["upload_file"]["error"]);
				}
				$_SESSION['TOKEN'] = $random;
				$_SESSION['name'] = $_FILES['upload_file']['name'];
				$_SESSION['size'] = $_FILES['upload_file']['size']/1048576;
				$text = file_get_contents("./template/index.php");
				$text = str_replace("TOKENSOONREPLACED", $random, $text);
				$text = str_replace("SIZESOONREPLACED", number_format((float)$_FILES['upload_file']['size']/1048576, 3, '.', ''), $text);
				$handle = fopen("./x/".$random."/index.php", "w");
				fwrite($handle, $text, strlen($text));
				fclose($handle);
			}
		}
	}
}

?>