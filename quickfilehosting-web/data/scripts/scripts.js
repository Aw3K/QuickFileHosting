var lock = false;
var VIEW = 0;
function viewFILE() {
	var x = document.getElementById("viewConatiner");
	var y = document.getElementById("showFile");
	if (VIEW == 0) {
		x.style.display = "initial";
		y.style.height = "auto";
		VIEW++;
	} else {
		x.style.display = "none";
		y.style.height = "17px";
		VIEW--;
	}
}

function upload_file_ajax(e)
{
	var bar = $('#bar1');
	var percent = $('#percent1');
	var file = document.getElementById("upload_file");
	var formm = document.getElementById("myForm");
	if (typeof file.files[0] === 'undefined' || file.files[0].length == 0) {
		alert("File not selected.");
		$("#myForm").submit(function(e){
			return false;
		});
		document.getElementById("progress_div").style.display = "none";
		return;
	}
	if (file.files[0].size <= maxsize){
		if (file.files[0].length != 0) {
			document.getElementById("progress_div").style.display = "block";
			var percentVal = '0%';
			bar.width(percentVal);
			percent.html(percentVal);
		}
		
		$('#myForm').ajaxForm({
		beforeSubmit: function() {
			formm.style.display = 'none';
		},

		uploadProgress: function(event, position, total, percentComplete) {
			var percentVal = percentComplete + '%';
			bar.width(percentVal);
			percent.html(percentVal);
		},
		
		error: function (response) {
		},
		
		success: function(responseText) {
			var percentVal = '100%';
			bar.width(percentVal);
			percent.innerHTML = percentVal;
			var file = document.getElementById("upload_file");
			file.value = "";
			setTimeout( function(){
				$('#out').load('success.php');
				document.getElementById("progress_div").style.display = "none";
				var formm = document.getElementById("myForm");
				formm.style.display = 'initial';
			}, 2000 );
		}
	  });
	} else {
		alert("File too big.");
		$("#myForm").submit(function(e){
			return false;
		});
		var formm = document.getElementById("myForm");
		formm.style.display = 'initial';
		document.getElementById("progress_div").style.display = "none";
	}
}

function start() {
	var file = document.getElementById("upload_file");
	file.value = "";
	loop();
	lock = true;
}

function splitNum(x) {
	var y = x.split(".");
	var out = y[0] + "." + y[1].slice(0, 3);
	return out;
}

function loop() {
	if (lock == true) return;
	worker();
}

function worker() {
	var time = setTimeout( worker, 100 );
	var file = document.getElementById("upload_file");
	if (file.files.length != 0) {
		clearTimeout(time);
		var size = file.files[0].size/1048576;
		document.getElementById('out').innerHTML = "<div class='selected'>" + file.files[0].name + " - " + splitNum(size.toString()) + "MB</div>";
		lock = false;
	}
}

function openInNewTab(url) {
  var win = window.open(url, "_blank");
  win.focus();
}