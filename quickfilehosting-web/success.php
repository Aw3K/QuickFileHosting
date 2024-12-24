<?php
session_start();
echo "<div class='selected'>";
if (isset($_SESSION['TOKEN']) && !empty($_SESSION['TOKEN'])) 
{
	echo "<span style='font-size: 110%; cursor: pointer;' onclick=\"openInNewTab('https://quickfilehosting.ddns.net/x/" . $_SESSION['TOKEN'] . "')\">" . $_SESSION['name'] . "</span><input style='max-width: 800px; width: 100% !important; border-top: 2px solid green; border-bottom: 2px solid green;' onclick=\"this.setSelectionRange(0, this.value.length)\"	value='quickfilehosting.ddns.net/x/" . $_SESSION['TOKEN'] . "' readonly>";
} else {
	echo "<input style='max-width: 800px; width: 100% !important; border-top: 2px solid green; border-bottom: 2px solid green;' value=' An Error had occured, contact owner for more information.' readonly>";
}

$error = $_SESSION['errorcode'];
$out = "";
if($error == "NOCONNECTED") $out = "<span style=\"color: red;\">ERROR:</span> Could not connect to the server Database.";
else if($error == "BIG") $out = "<span style=\"color: red;\">ERROR:</span> Sent file was too big, max 512MB.";
else if($error == "NOSELECTED") $out = "<span style=\"color: red;\">ERROR:</span> File name was an empty string.";
else if($error == "SEND51") $out = "File successfuly uploaded to server.";
else if($error == "SEND52") $out = "File successfuly uploaded to server.";
else if($error == "NOMOVE") $out = "<span style=\"color: red;\">ERROR:</span> Could not move file to it's new location on the server.";
else $out = $error;

echo "<span style='width: auto;'>".$out."</span>";
echo "</div>";
unset($_SESSION['TOKEN'], $_SESSION['name'], $_SESSION['size']);
?>