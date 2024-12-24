<?php
require "../config.php";

function jsonResponse($status, $code, $fileid = 0){
	$response = ['status' => $status, 'code' => $code, 'token' => $fileid];
	header('Content-type: application/json');
	exit (json_encode( $response ));
}

if (isset($_POST["mode"])) $_POST["mode"] = strip_tags($_POST["mode"]);
if (isset($_POST["email"])) $_POST["email"] = strip_tags($_POST["email"]);
if (isset($_POST["hash"])) $_POST["hash"] = strip_tags($_POST["hash"]);

if (!$conn) {jsonResponse("ERROR", "DATABASE_NOT_CONNECTED");}
else {
	if ($_SERVER["REQUEST_METHOD"] === "POST") {
		if (isset($_POST["mode"])){
			if ($_POST["mode"] == "accountActivation"){
				if (isset($_POST["email"]) && isset($_POST["hash"])){
					$_POST["email"] = mysqli_real_escape_string($conn, $_POST["email"]);
					$res = $conn->query("SELECT * FROM `users` WHERE `email` = '{$_POST["email"]}';");
					if ($data = $res->fetch_assoc()){
						if ($data["active"] != 0) jsonResponse("ERROR", "USER_ALREADY_ACTIVE");
						if ($data["hash"] == $_POST["hash"]){
							$headers  = 'MIME-Version: 1.0' . "\r\n";
							$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
							$headers .= "From: quickfilehosting@gmail.com\r\n"."X-Mailer: php";
							$subject = "QuickFileHosting - account activation";
							$message = "<html>
											<head>
												<style>
													.text {
														color: green;
														margin: 5px;
													}
													.container {
														width: 596px;
														border: 2px solid green;
														background-color: #191d21;
														text-align: left;
													}
												</style>
											</head>
											<body>
												<center><img src='https://quickfilehosting.ddns.net/data/images/logo.png'>
												<div class='container'>
													<p class='text'>An account was created at quickfilehosting.ddns.net using that e-mail address.</p>
													<p class='text'>If that wasn't You, ignore that message.</p><br>
													<p class='text'>Account information:</p>
													<p class='text'>Username: ". $data['username'] .",</p>
													<p class='text'>create date: ". date("d.m.y, H:i:s", $data["createdate"]) .".</p><br>
													<p class='text'>To activate account click in link below.</p>
													<p class='text'><a href='https://quickfilehosting.ddns.net/account/activate.php?email=". $_POST["email"] ."&hash=". $_POST["hash"] ."'>https://quickfilehosting.ddns.net/account/activate.php?email=". $_POST["email"] ."&hash=". $_POST["hash"] ."</a></p><br>
													<p class='text'>Thanks for using our page!</p>
													<p class='text'>QuickFileHosting Team</p><br>
													<p class='text'>This message was generated automaticaly, please do not responde.</p>
												</div></center>
											</body>
										</html>";
							if (mail($_POST["email"],$subject,$message,$headers)) jsonResponse("SUCCESS", "MAIL_SEND");
							else jsonResponse("ERROR", "MAIL_CANT_SEND");
						} else jsonResponse("ERROR", "HASH_MISMATCH");
					} else jsonResponse("ERROR", "EMAIL_DONT_EXIST");
				} else jsonResponse("ERROR", "DATA_NOT_SET");
			} else jsonResponse("ERROR", "MODE_NOT_SUPPORTED");
		} else jsonResponse("ERROR", "MODE_NOT_SPECIFIED");
	} else jsonResponse("ERROR", "PROTOCOL_UNKNOWN");
}
?>
