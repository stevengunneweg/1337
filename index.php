
<!DOCTYPE HTML>
<html>
<head>
	<title>1337</title>
	<?php include_once("g_analytics.php") ?>
	<link rel="shortcut icon" href="assets/favicon.ico"/>
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="js/main.js"></script>
	
	<link rel='stylesheet' href='css/style.css' type='text/css'>
</head>
<body>
	<div class="score_field" id="today">
		<h1>Today</h1>
		<p id="serverClock">00:00:00<p>
		<p id="date_today"><script type="text/javascript">printToday('date_today');</script></p>
		<table id="table_today">
		</table>
	</div>
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
		if you do not get it, <a href="info.html" target="blanc">read me</a>
	</div>
</body>
</html>
