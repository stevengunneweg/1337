<!DOCTYPE HTML>
<html>
<head>
	<link rel="shortcut icon" href="assets/favicon.ico"/>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script type="text/javascript">
		window.onload = function() {
			printScore('table_today', 'getToday');

			printYesterday('time');
			printScore('table_score', 'getYesterday');
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
						alert(msg)
						location.reload();
					}
				});
			}
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
		function printWeek(target) {
			var date = new Date();
			document.getElementById(target).innerHTML = 'Week ' + getWeekNumber(date);
		}
		function printMonth(target) {
			var date = new Date();
			document.getElementById(target).innerHTML = 'Month ' + date.getMonth();
		}
		function printYear(target) {
			var date = new Date();
			document.getElementById(target).innerHTML = 'Year ' + date.getFullYear();
		}
		function printText(target, text) {
			var date = new Date();
			document.getElementById(target).innerHTML = text;
		}
		function printScore(target, action) {
			var targetTable = document.getElementById(target);
			targetTable.innerHTML = '';
			$.ajax({
			    data: 'action=' + action,
				url: 'manager.php',
				method: 'POST', // or GET
				success: function(msg) {
					result = JSON.parse(msg);
					for (var i = 0; i < result.length; i++) {
						addToList(target, result[i].name, result[i].moment, result[i].day);
					}
					if (result.length == 0) {
						addToList(target, 'This list is currently empty', '', '');
					}
				}
			});
		}
		function addToList(table_id_name, name, timestamp, day) {
			var table = document.getElementById(table_id_name);
			var row = table.insertRow(-1);
			var cell_score = row.insertCell();
			cell_score.setAttribute('title', day);
			var cell_name = row.insertCell();
			cell_score.className = 'score';
			cell_name.className = 'name';
			cell_name.innerHTML = name;
			cell_score.innerHTML = timestamp;
		}

		function getWeekNumber(d) {
			// Copy date so don't modify original
			d = new Date(+d);
			d.setHours(0,0,0);
			// Set to nearest Thursday: current date + 4 - current day number
			// Make Sunday's day number 7
			d.setDate(d.getDate() + 4 - (d.getDay()||7));
			// Get first day of year
			var yearStart = new Date(d.getFullYear(),0,1);
			// Calculate full weeks to nearest Thursday
			var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7)
			// Return array of year and week number
			return weekNo;
		}
		function makeActive(button) {
			var buttons = document.getElementsByTagName('button');
			for (var ding in buttons) {
				buttons[ding].className = '';
			}
			button.className = 'active';
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

		#score {
			float: right;
			border-left: 1px solid #ccc;
		}
			#score .active {
				background: #999;
				color: #000;
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
	<!-- <div class="score_field" id="yesterday">
		<h1>Yesterday</h1>
		<p id="date_yesterday"><script type="text/javascript">printYesterday('date_yesterday');</script></p>
		<table id="table_yesterday">
		</table>
	</div> -->
	<div class="score_field" id="score">
		<p>
			<button onclick="makeActive(this); printYesterday('time'); printScore('table_score', 'getYesterday');" class="active">Yesterday</button>
			<button onclick="makeActive(this); printWeek('time'); printScore('table_score', 'getWeek');">Week</button>
			<button onclick="makeActive(this); printMonth('time'); printScore('table_score', 'getMonth');">Month</button>
			<button onclick="makeActive(this); printYear('time'); printScore('table_score', 'getYear');">Year</button>
			<button onclick="makeActive(this); printText('time', ''); printScore('table_score', 'getTop');">All time</button>
		</p>
		<p id="time">
		</p>
		<table id="table_score">
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