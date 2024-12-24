function fileManipulation(mode, fileid) {
	var URL = './filemanipulation.php';
	var xhr = new XMLHttpRequest();

	if (mode == "RECA" || mode == "DELA") {
		xhr.open('GET', URL + "?mode=" + mode, true);
	}
	else if (mode == "REC" || mode == "DEL") {
		xhr.open('GET', URL + "?mode=" + mode + "&fileid=" + fileid, true);
	}
	else if (mode == "PASSOUT") {
		if (confirm("Are You sure that wants to clear password for that file?")) {
			xhr.open('GET', URL + "?mode=" + mode + "&fileid=" + fileid, true);
		} else {
			 pageState("LFILES");
			 return;
		}
	}
	else if (mode == "REN") {
		var name = prompt("Please enter new file name:");
		if (name == null || name == "") {
			pageState("LFILES");
			return;
		} else {
			xhr.open('GET', URL + "?mode=" + mode + "&fileid=" + fileid + "&name=" + name, true);
		}
	}
	else if (mode == "PASS") {
		var pass = prompt("Please enter new file password:");
		if (pass == null || pass == "") {
			pageState("LFILES");
			return;
		} else {
			xhr.open('GET', URL + "?mode=" + mode + "&fileid=" + fileid + "&pass=" + pass, true);
		}
	}

	xhr.onload = function() {
		if (xhr.responseText == "SUCCESS") {
			$('#filelist').load("profilefilelist.php");
		} else if (xhr.responseText == "SUCCESSR") {
			$('#filelist').load("profilefilerecover.php");
		} else alert(xhr.responseText);
	}

	xhr.send()
}

function pageState(x) {
	if (x == "LFILES") $('#filelist').load("profilefilelist.php");
	else if (x == "RFILES") $('#filelist').load("profilefilerecover.php");
}

function openInNewTab(x) {
	window.open(x, '_blank');
}
