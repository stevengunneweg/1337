<?php
include('../db.php');

date_default_timezone_set("Europe/Amsterdam");

$dbh = null;

$select = "SELECT name, time, SUBSTRING_INDEX(time, ' ', 1) AS day, SUBSTRING_INDEX(time, ' ', -1) AS moment FROM listing ";
$isLeet = " AND (HOUR(time) = 13 AND MINUTE(time) = 37) ";
$order = " ORDER BY moment ASC LIMIT 30";

$queryDay = $select." WHERE deleted IS NULL AND DATE(time) = CURDATE() ".$isLeet.$order;
$queryYesterday = $select." WHERE deleted IS NULL AND DATE(time) = DATE_ADD(CURDATE(), INTERVAL -1 DAY) ".$isLeet.$order;
$queryWeek = $select." WHERE deleted IS NULL AND WEEKOFYEAR(time) = WEEKOFYEAR(CURDATE()) AND YEAR(time) = YEAR(CURDATE()) ".$isLeet.$order;
$queryMonth = $select." WHERE deleted IS NULL AND MONTH(time) = MONTH(CURDATE()) AND YEAR(time) = YEAR(CURDATE()) ".$isLeet.$order;
$queryYear = $select." WHERE deleted IS NULL AND YEAR(time) = YEAR(CURDATE()) ".$isLeet.$order;
$queryTop = $select."WHERE deleted IS NULL AND true".$isLeet.$order;

try {
	$dbh = new PDO('mysql:host='.$host.';dbname='.$db_name.';port:3306', $username, $pass);
	if (isset($_POST['action'])) {
		switch($_POST['action']) {
			case 'new':
				pushScore($_POST['data']);
				break;
			case 'getToday':
				getScore($queryDay);
				break;
			case 'getYesterday':
				getScore($queryYesterday);
				break;
			case 'getWeek':
				getScore($queryWeek);
				break;
			case 'getMonth':
				getScore($queryMonth);
				break;
			case 'getYear':
				getScore($queryYear);
				break;
			case 'getTop':
				getScore($queryTop);
				break;
			case 'getNameWithIp':
				getNameWithIp();
				break;
			case 'getServerTime':
				getServerTime();
				break;
			case 'newUser':
				newUser($_POST['data']);
				break;
		}
	}
} catch (PDOException $e) {
	print "Error connecting to database";
	die();
}

function _getCurrentServerTime() {
	$cur_micro = microtime(true);
	$micro = sprintf("%06d",($cur_micro - floor($cur_micro)) * 1000000);
	$date = new DateTime( date('Y-m-d H:i:s.'.$micro,$cur_micro) );
	return $date->format("Y-m-d H:i:s.u");
}

function getScore($query) {
	global $dbh;

	$stmt = $dbh->prepare($query);
	$stmt->execute();
	$result = $stmt->fetchAll();
	echo json_encode($result);
}

function getNameWithIp() {
	global $dbh;

	$stmt = $dbh->prepare("SELECT name FROM listing WHERE ip = :ip ORDER BY time DESC LIMIT 1");
	$stmt->execute(array(':ip'=>$_SERVER['REMOTE_ADDR']));
	$result = $stmt->fetchAll();
	echo json_encode($result);
}

function hasRecordToday($ip) {
	global $dbh, $isLeet;

	$stmt = $dbh->prepare("SELECT count(ip) as ip_count FROM listing WHERE DATE(time) = CURDATE() AND TIME_TO_SEC(time) > TIME_TO_SEC(CURTIME()) - 20 AND ip = '".$ip."'");
	$stmt->execute();
	$result = $stmt->fetchAll();

	return ($result[0]['ip_count'] > 0);
}

function pushScore($name) {
	if (strlen($name) > 20) {
		print 'Name is too long';
		return;
	} else if (strlen($name) < 2) {
		print 'Name is too short';
		return;
	} else if (strpos($_SERVER['HTTP_REFERER'], '1337online.com') === false) {
		print 'You sneaky boy...';
		return;
	} else if (hasRecordToday($_SERVER['REMOTE_ADDR'])) {
		print 'You need to wait 20 after your previous post';
		return;
	}

	global $dbh;

	$_time = _getCurrentServerTime();

	$stmt = $dbh->prepare('INSERT INTO listing (name, time, ip) VALUES (:name, :time, :ip)');
	$stmt->execute(array(':name'=>$name, ':time'=>$_time, ':ip'=>$_SERVER['REMOTE_ADDR']));
	print 'ok';
}

function getServerTime() {
	echo _getCurrentServerTime();
}

function newUser($data) {
	global $dbh;

	$data = json_decode($data);
	$name = $data[0];
	$pass = $data[1];

	$stmt = $dbh->prepare('SELECT name FROM users WHERE name = :name');
	$stmt->execute(array(':name'=>$name));
	$result = $stmt->fetchAll();

	if (count($result) > 0) {
		echo 'user with this name already exists';
		return;
	}

	$stmt = $dbh->prepare('INSERT INTO users (name, pass) VALUES (:name, :pass)');
	$stmt->execute(array(':name'=>$name, ':pass'=>sha1($pass)));
	$result = $stmt->fetchAll();
	echo 'succes';
}
?>
