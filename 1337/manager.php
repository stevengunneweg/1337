<?php
include('db.php');

$dbh = null;
try {
	$dbh = new PDO('mysql:host='.$host.';dbname='.$db_name, $username, $pass);
	if (isset($_POST['action'])) {
		switch($_POST['action']) {
			case 'new':
				pushScore($_POST['data']);
				break;
			case 'getToday':
				getDayScore();
				break;
			case 'getYesterday':
				getYesterdayScore();
				break;
		}
	}
} catch (PDOException $e) {
	print "Error!: " . $e->getMessage() . "<br/>";
	die();
}


function getDayScore() {
	global $dbh;

	$stmt = $dbh->prepare('SELECT name, time FROM listing WHERE time >= CURDATE() AND (HOUR(time) = 13 AND MINUTE(time) = 37) ORDER BY time ASC LIMIT 30');
	$stmt->execute();
	$result = $stmt->fetchAll();
	echo json_encode($result);
}
function getYesterdayScore() {
	global $dbh;

	$stmt = $dbh->prepare('SELECT name, time FROM listing WHERE time >= DATESUB(CURDATE() - INTERVAL 1 DAY) AND (HOUR(time) = 13 AND MINUTE(time) = 37) ORDER BY time ASC LIMIT 30');
	$stmt->execute();
	$result = $stmt->fetchAll();
	echo json_encode($result);
}
function pushScore($name) {
	if (strlen($name) < 20) {
		global $dbh;

		$cur_micro = microtime(true);
		$micro = sprintf("%06d",($cur_micro - floor($cur_micro)) * 1000000);
		$date = new DateTime( date('Y-m-d H:i:s.'.$micro,$cur_micro) );
		$_time = $date->format("Y-m-d H:i:s.u");
		
		$stmt = $dbh->prepare('INSERT INTO listing (name, time) VALUES (:name, :time)');
		$stmt->execute(array(':name'=>$name, ':time'=>$_time));
	} else {
		print 'name is too long';
	}
}
?>