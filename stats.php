<?php
/*
---- Stats ----

✅ amount of tries
times 1st
times 2nd
times 3rd
best winstreak
✅ best attempt
current winstreak
✅ average post time
*/

include('./db.php');

date_default_timezone_set("Europe/Amsterdam");
$dbh = null;
$name = $_GET['name'];

try {
	$dbh = new PDO('mysql:host='.$host.';dbname='.$db_name.';port:3306', $username, $pass);
} catch (PDOException $e) {
	print "Error connecting to database";
	die();
}

function getTries() {
	global $dbh, $name;

	$query = "SELECT count(*) FROM listing WHERE name = :name";
	$stmt = $dbh->prepare($query);
	$stmt->execute(array(':name'=>$name));
	$result = $stmt->fetchAll();
	return $result[0][0];
}
function getBestTry() {
	global $dbh, $name;

	$isLeet = " AND (HOUR(time) = 13 AND MINUTE(time) = 37) ";
	$query = "SELECT time FROM listing WHERE name = :name ".$isLeet."ORDER BY SUBSTRING(time FROM 12) ASC";
	$stmt = $dbh->prepare($query);
	$stmt->execute(array(':name'=>$name));
	$result = $stmt->fetchAll();
	return $result[0][0];
}
function getAvgTime() {
	global $dbh, $name;

	$query = "SELECT SUBSTRING(time FROM 12) as time FROM listing WHERE name = :name";
	$stmt = $dbh->prepare($query);
	$stmt->execute(array(':name'=>$name));

	$total = 0;
	$count = 0;
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$total += intval(str_replace('.', '', str_replace(':', '', $row['time'])));
		$count++;
    }
	$avg = $total / $count;
	$result = substr($avg,0,2).':'.substr($avg,2,2).':'.substr($avg,4,2).'.'.substr($avg,6);
	return $result;
}
function hasAchievement($achievement) {
	return false;
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>1337 Stats - <?php echo $name; ?></title>
	<?php include_once("g_analytics.php") ?>
	<link rel="shortcut icon" href="assets/favicon.ico"/>

	<style type="text/css">
		body, html {
			margin: 0px;
			padding: 0px;
			font-family: arial;
		}
		#container {
			width: 70%;
			min-width: 350px;
			margin: auto;
		}
		#stats, #achievements {
			float: left;
			margin: 50px;
		}
		#stats {
		}
			#stats table {
				color: #555;
			}
				#stats table tr td:first-child {
					width: 500px;
				}
		#achievements {
			max-width: 520px;
		}
			#achievements #achievement_list {
			}
				#achievements .achievement {
					width: 100px;
					height: 100px;
					float: left;
					background: url(../assets/lock.png) no-repeat;
					background-size: 60% 70%;
					background-position: center;
					background-color: #007fff;
					margin: 15px;
					border-radius: 10px;
				}
	</style>
</head>
<body>
	<div id="container">
		<h1><?php echo $name; ?></h1>
		<div id="stats">
			<h2>Stats</h2>
			<table>
				<tr>
					<td>tries</td>
					<td><?php echo getTries(); ?></td>
				</tr>
				<!-- <tr>
					<td>times 1st</td>
					<td>&lt;amount&gt;</td>
				</tr> -->
				<!-- <tr>
					<td>times 2nd</td>
					<td>&lt;amount&gt;</td>
				</tr> -->
				<!-- <tr>
					<td>times 3rd</td>
					<td>&lt;amount&gt;</td>
				</tr> -->
				<!-- <tr>
					<td>winstreak</td>
					<td>&lt;amount&gt;</td>
				</tr> -->
				<tr>
					<td>best attempt</td>
					<td><?php echo getBestTry(); ?></td>
				</tr>
				<!-- <tr>
					<td>current winstreak</td>
					<td>&lt;amount&gt;</td>
				</tr> -->
				<!-- <tr>
					<td>current poststreak</td>
					<td>&lt;amount&gt;</td>
				</tr> -->
				<tr>
					<td>avg. post time</td>
					<td><?php echo getAvgTime(); ?></td>
				</tr>
			</table>
		</div>
		<!-- <div id="achievements">
			<h2>Achievements</h2>
			<div id="achievement_list">
				<div class="achievement" style="background: url(../assets/first.png) no-repeat; background-size: 100% 100%; background-position: center; background-color: #007fff;"></div>
				<div class="achievement"></div>
				<div class="achievement"></div>
				<div class="achievement"></div>
				<div class="achievement"></div>
				<div class="achievement"></div>
				<div class="achievement"></div>
				<div class="achievement"></div>
				<div class="achievement"></div>
				<div class="achievement"></div>
				<div class="achievement"></div>
				<div class="achievement"></div>
				<div class="achievement"></div>
			</div>
		</div> -->
	</div>
</body>
</html>
