<?php
session_start();
require "../../config.php";

$token = "TOKENSOONREPLACED";
$fileDwnLink = "/files/download.php?token=TOKENSOONREPLACED";

if (!$conn) {
	die ($conn -> mysqli_error);
}

$res = $conn -> query("SELECT * FROM `files` WHERE `token` = '$token'");
$data = mysqli_fetch_array($res);
$locks = $data['locked'];
$file = "../../files/data/".$data['hash']."/".$data['fname'];

$finfo = new finfo();
$fileinfo = $finfo->file($file, FILEINFO_MIME);
$type = explode("/", $fileinfo); //$type[0]

if (isset($_POST['passwd']) && !empty($_POST['passwd'])) {
	$passwd = md5(mysqli_real_escape_string($conn, $_POST['passwd']));
	if ($passwd == $data['pass']) {
		$_SESSION[$token]['AUTH'] = $token;
	}
	unset($_POST['passwd']);
}

$show = true;
if ((!empty($data['pass']) && $_SESSION[$token]['AUTH'] != $token) || $locks == "true") $show = false;
if ($show) {
?>
<html>
	<head>
		<title>Quick File Hosting - File download</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="../../data/css/style.css">
		<link rel="icon" href="../../data/images/icon.png">
		<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="https://malsup.github.io/jquery.form.js"></script>
		<script type="text/javascript" src="https://underscorejs.org/underscore-min.js"></script>
		<script type="text/javascript" src="../../data/scripts/scripts.js"></script>
	</head>
	<body>
		<?php include "../../data/php/header.html"; ?>
		<div class="container">
			<?php
			if ($locks == "true") {
			?>
			<div class="text" style="margin: 20px; text-align: center; width: auto; height: auto; font-weight: bold; font-size: 120%;">FILE EXPIRED OR LOCKED</div>
			<?php
			} else {
				?>
				<table style="color: green; margin-top: 20px;" align="center">
					<tbody>
						<tr>
							<td>
								File requested to download:<br><span style="color: green; border: 2px solid green; font-size: 120%; font-weight: bold;">
								<?php 
								$res = $conn -> query("SELECT * FROM `files` WHERE `token` = '$token'");
								$dane = mysqli_fetch_array($res);
								echo $dane['fname'];
								?> - SIZESOONREPLACEDMB </span><br>
								<span style="margin: 0px auto auto 0px; color: green;">
								Download counts: <?php echo $dane['dcount']; ?></span>
							</td>
							<td style="width: 10%;">
							</td>
							<td>
								<a href="<?php echo $fileDwnLink; ?>"><button id="submitfile">DOWNLOAD FILE</button></a>
							</td>
						</tr>
					</tbody>
				</table>
				<?php
				$fileDwnLink .= "&mode=VIEW";
				if ($type[0] == "audio") {
				?>
					<div id="showFile">
						<div class="viewTextButton" onclick="viewFILE();">Click there to view file.</div>
						<audio id="viewConatiner" controls>
							<source src="<?php echo $fileDwnLink; ?>">
						</audio>
					</div>
				<?php
				}
				else if ($type[0] == "image") {
				?>
					<div id="showFile">
						<div class="viewTextButton" onclick="viewFILE();">Click there to view file.</div>
						<img id="viewConatiner" style="width: auto; height: auto; margin: .5%;" src="<?php echo $fileDwnLink; ?>">
					</div>
				<?php	
				}
				else if ($type[0] == "video") {
				?>
					<div id="showFile">
						<div class="viewTextButton" onclick="viewFILE();">Click there to view file.</div>
						<video id="viewConatiner" style="width: auto; height: auto; margin: .5%;" controls>
							<source src="<?php echo $fileDwnLink; ?>">
						</video>
					</div>
				<?php
				}
				else if ($type[0] == "text" && $data['size'] < 1048576) {
				?>
					<div id="showFile">
						<div class="viewTextButton" onclick="viewFILE();">Click there to view file.</div>
						<div id="viewConatiner" style="text-align: left;">
							<?php
							$content="<div class='viewTEXTstyle'><pre>".htmlspecialchars(file_get_contents("$file"))."</pre></div>";
							echo $content;
							?>
						</div>
					</div>
				<?php
				}
			}
			?>
			</br>
			<div class="loggedin"><center><span class="text"><?php
			
			if (isset($_SESSION['login']) && !empty($_SESSION['login']) && isset($_SESSION['hash']) && !empty($_SESSION['hash'])) echo "Logged as: " . $_SESSION['login']." <a href='account/logout.php'>Logout</a></span></center>";
			else {
				echo "Sends files as: Guest, <a href='account/index.php'>login</a> for more possiblities!</span></center>";
			}
			
			?></div>
		</div>
	</body>
</html>
<?php
exit;
} else {
?>
<html>
	<head>
		<title>Quick File Hosting - Download</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="/data/css/style.css">
		<link rel="stylesheet" type="text/css" href="/data/css/styleaccounts.css">
	</head>
	<body>
		<?php include "../../data/php/header.html"; ?>
		<div class="container">
			<?php if ($locks == "false"){ ?>
				<center><div style="border: 4px solid green; max-width: 250px; margin: 10px; border-radius: 25px; background-color: rgba(0,0,0,.2);"><form method="post" action="">
				<br><input class="logsignt" type="password" name="passwd" placeholder="Password"><br><br>
				<input class="logsign" type="submit" name="subbutton" value="Submit">
				</form></div></center>
			<?php } else { ?>
				<div class="text" style="margin: 20px; text-align: center; width: auto; height: auto; font-weight: bold; font-size: 120%;">FILE EXPIRED OR LOCKED</div>
			<?php } ?>
			<div class="loggedin"><center><span class="text"><?php
			
			if (isset($_SESSION['login']) && !empty($_SESSION['login']) && isset($_SESSION['hash']) && !empty($_SESSION['hash'])) echo "Logged as: " . $_SESSION['login']." <a href='account/logout.php'>Logout</a></span></center>";
			else {
				echo "Sends files as: Guest, <a href='account/index.php'>login</a> for more possiblities!</span></center>";
			}
			
			?></div>
		</div>
	</body>
</html>
<?php
exit;
}
?>