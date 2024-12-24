<?php
require "../config.php";

function jsonResponse($status, $code, $fileid = 0){
	$response = ['status' => $status, 'code' => $code, 'token' => $fileid];
	header('Content-type: application/json');
	exit (json_encode( $response ));
}

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null;
if (!$conn) {jsonResponse("ERROR", "DATABASE_NOT_CONNECTED");}
else {
	if ($_SERVER["REQUEST_METHOD"] === "POST") {
		if (isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
			$random = generateRandomString(10);
			$hash = md5(generateRandomString(20));
			if ($_FILES['file']['size'] > $SETTINGS['maxsize']) {jsonResponse("ERROR", "FILE_TOO_BIG");}
			else if (strlen($_FILES['file']['name']) == 0) {jsonResponse("ERROR", "FILE_NAME_EMPTY");}
			else {
				$datee = new DateTime(date('Y-m-d'));
				$exdate = new DateTime(date('Y-m-d'));
				$exdate -> modify ('+1 month');
				if (!$conn) {jsonResponse("ERROR", "DATABASE_NOT_CONNECTED");}
				else {
					$uploadfile=$_FILES["file"]["tmp_name"];
					mkdir("../x/".$random);
					mkdir("../files/data/".$hash);
					$folder="../files/data/".$hash."/";
					if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
						if (move_uploaded_file($_FILES["file"]["tmp_name"], $folder.$_FILES["file"]["name"])) {
							if (empty($authHeader)) {
								$conn -> query("INSERT INTO `files` (`token`, `hash`, `created`, `fname`, `size`, `expired`, `userip`, `remove`, `pass`) VALUES ('$random', '$hash', '".$datee->format('Y-m-d')."', '".$_FILES['file']['name']."', " . $_FILES['file']['size'] . ", '".$exdate->format('Y-m-d')."', '" . $_SERVER['REMOTE_ADDR'] . "', 'false', '')");
								$SUCCESS = "FILE_UPLOADED_NOUSER";
							} else {
								$res = $conn -> query("SELECT * FROM `users` WHERE `hash` = '" . $authHeader . "'");
								if ($userdata = $res->fetch_assoc()){
									$conn -> query("INSERT INTO `files` (`token`, `hash`, `created`, `fname`, `size`, `expired`, `userip`, `remove`, `owner`, `pass`) VALUES ('$random', '$hash', '".$datee->format('Y-m-d')."', '".$_FILES['file']['name']."', '" . $_FILES['file']['size'] . "', '".$exdate->format('Y-m-d')."', '" . $_SERVER['REMOTE_ADDR'] . "', 'false', '" . $userdata['id'] . "', '')");
									$SUCCESS = "FILE_UPLOADED_USER";
								} else {
									$conn -> query("INSERT INTO `files` (`token`, `hash`, `created`, `fname`, `size`, `expired`, `userip`, `remove`, `pass`) VALUES ('$random', '$hash', '".$datee->format('Y-m-d')."', '".$_FILES['file']['name']."', " . $_FILES['file']['size'] . ", '".$exdate->format('Y-m-d')."', '" . $_SERVER['REMOTE_ADDR'] . "', 'false', '')");
									$SUCCESS = "FILE_UPLOADED_NOUSER";
								}
							}
						} else {
							jsonResponse("ERROR", "FILE_MOVE_FAILED");
						}
					} else {
						jsonResponse("ERROR", $_FILES["file"]["error"]);
					}
					$text = file_get_contents("../template/index.php");
					$text = str_replace("TOKENSOONREPLACED", $random, $text);
					$text = str_replace("SIZESOONREPLACED", number_format((float)$_FILES['file']['size']/1048576, 3, '.', ''), $text);
					$handle = fopen("../x/".$random."/index.php", "w");
					fwrite($handle, $text, strlen($text));
					fclose($handle);
					jsonResponse("SUCCESS", $SUCCESS, $random);
				}
			}
		} else {
			jsonResponse("ERROR", "FILE_UPLOAD_FAILED", $_FILES["file"]["error"]);
		}
	} else {
		jsonResponse("ERROR", "PROTOCOL_UNKNOWN");
	}
}
?>
