<?php
error_reporting(0);
$conn = new mysqli('localhost', '', '', '');

function delTree($dir) {
   $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

$pom=$conn->query("SELECT * FROM `files`");
while($rows=$pom->fetch_assoc())
{
	if ($rows['remove'] == "true") {
		$del = $rows['hash'];
		if (file_exists($del)) {
			delTree($del);
			rmdir($del);
			$dirs = scandir("./");
			if (!in_array($rows['hash'], $dirs)) {
				$conn -> query("UPDATE `files` SET `remove` = 'DONE',`locked` = 'true' WHERE `token` = '" . $rows['token'] ."'");
			}
		} else {
			$conn -> query("UPDATE `files` SET `remove` = 'DONE',`locked` = 'true' WHERE `token` = '" . $rows['token'] ."'");
		}
	}
}

//passress remove
$conn -> query("UPDATE `users` SET `passres` = ''");

//not active accounts remove
$conn -> query("DELETE FROM `users` WHERE `active` = 0;");

?>