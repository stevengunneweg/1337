<!DOCTYPE HTML>
<html>
<head>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script type="text/javascript">
		window.onload = function() {
			$.ajax({
			    data: 'action=getToday',
				url: 'manager.php',
				method: 'POST', // or GET
				success: function(msg) {
					result = JSON.parse(msg);
					for (var i = 0; i < result.length; i++) {
						var date = new Date(result[i].time);
						addToList('table_today', result[i].name, result[i].time.split(" ")[1]);
					}
				}
			});
			$.ajax({
			    data: 'action=getYesterday',
				url: 'manager.php',
				method: 'POST', // or GET
				success: function(msg) {
					result = JSON.parse(msg);
					for (var i = 0; i < result.length; i++) {
						var date = new Date(result[i].time);
						addToList('table_yesterday', result[i].name, result[i].time.split(" ")[1]);
					}
				}
			});
		}
		function send() {
			if (!document.getElementById('name_field').value) {
				alert('fill in a name!\nHURRRYYY!!');
				document.getElementById('name_field').focus();
			} else {
				$.ajax({
				    data: 'action=new' + '&data=' + document.getElementById('name_field').value,
					url: 'manager.php',
					method: 'POST', // or GET
					success: function(msg) {
						location.reload();
					}
				});
			}
		}
		function addToList(table_id_name, name, timestamp) {
			var table = document.getElementById(table_id_name);
			var row = table.insertRow(-1);
			var cell_score = row.insertCell();
			var cell_name = row.insertCell();
			cell_score.className = 'score';
			cell_name.className = 'name';
			cell_name.innerHTML = name;
			cell_score.innerHTML = timestamp;
		}

		function printToday(target) {
			var today = new Date();
			document.getElementById(target).innerHTML = today.getDate() + '/' + (today.getMonth() + 1) + '/' + today.getFullYear();
		}
		function printYesterday(target) {
			var yesterday = new Date();
			yesterday.setDate(yesterday.getDate() - 1);
			document.getElementById(target).innerHTML = yesterday.getDate() + '/' + (yesterday.getMonth() + 1) + '/' + yesterday.getFullYear();
		}
	</script>
	
	<style type="text/css">
		html, body {
			margin: 0px;
			padding: 0px;
			width: 100%;
			height: 100%;
			font-family: arial, helvetica;
		}
		#name {
			position: fixed;
			top: calc(40% - 75px);
			left: calc(50% - 150px);
			text-align: center;
		}
			#name input {
				width: 300px;
				font-size: 50px;
				border-left: none;
				border-right: none;
				text-align: center;
			}
		#button {
			position: fixed;
			top: calc(50% - 75px);
			left: calc(50% - 150px);
			text-align: center;
		}
			#button button {
				width: 300px;
				height: 150px;
				font-size: 70px;
			}
		.score_field {
			position: relative;
			width: 300px;
			height: 100%;
			padding-left: 20px;
			padding-right: 20px;
			text-align: center;
		}
			.score_field table {
				width: 100%;
			}
				.score_field table .name {
					text-align: left;
				}
				.score_field table .score {
					text-align: right;
				}
		#today {
			float: left;
			border-right: 1px solid #ccc;
		}
		#yesterday {
			float: right;
			border-left: 1px solid #ccc;
		}
	</style>
</head>
<body>
	<div class="score_field" id="today">
		<h1>Today</h1>
		<p id="date_today"><script type="text/javascript">printToday('date_today');</script></p>
		<table id="table_today">
		</table>
	</div>
	<div class="score_field" id="yesterday">
		<h1>Yesterday</h1>
		<p id="date_yesterday"><script type="text/javascript">printYesterday('date_yesterday');</script></p>
		<table id="table_yesterday">
		</table>
	</div>

	<div id="name">
		<input id="name_field" placeholder="name"></input>
	</div>
	<div id="button">
		<button onclick="send()">1337</button>
	</div>

	<div>
		if you do not get it <a href="/info.html" target="blanc">read me</a>
	</div>
</body>
</html>