<?php
include('db.php');

$dbh = null;


$select = "SELECT name, time, SUBSTRING_INDEX(time, ' ', 1) AS day, SUBSTRING_INDEX(time, ' ', -1) AS moment FROM listing ";
$isLeet = " AND (HOUR(time) = 13 AND MINUTE(time) = 37) ";
$order = " ORDER BY moment ASC LIMIT 30";


$queryDay = $select." WHERE DATE(time) = CURDATE() ".$isLeet.$order;
$queryYesterday = $select." WHERE DATE(time) = DATE_ADD(CURDATE(), INTERVAL -1 DAY) ".$isLeet.$order;
$queryWeek = $select." WHERE WEEKOFYEAR(time) = WEEKOFYEAR(CURDATE()) ".$isLeet.$order;
$queryMonth = $select." WHERE MONTH(time) = MONTH(CURDATE()) ".$isLeet.$order;
$queryYear = $select." WHERE YEAR(time) = YEAR(CURDATE()) ".$isLeet.$order;
$queryTop = $select."WHERE true".$isLeet.$order;

try {
	$dbh = new PDO('mysql:host='.$host.';dbname='.$db_name, $username, $pass);
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
		}
	}
} catch (PDOException $e) {
	print "Error connecting to database";
	die();
}


function getScore($query) {
	global $dbh;

	$stmt = $dbh->prepare($query);
	$stmt->execute();
	$result = $stmt->fetchAll();
	echo json_encode($result);
}

function hasRecordToday($ip) {
	global $dbh, $isLeet;

	$stmt = $dbh->prepare("SELECT count(ip) as ip_count FROM listing WHERE DATE(time) = CURDATE() AND ip = '".$ip."'");
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
	} else if (hasRecordToday($_SERVER['REMOTE_ADDR'])) {
		print 'Already posted today';
		return;
	// } else if (date("H") != 13 && date("i") != 37) {
	// 	print 'It is not 13:37';
	// 	return;
	}

	global $dbh;

	$cur_micro = microtime(true);
	$micro = sprintf("%06d",($cur_micro - floor($cur_micro)) * 1000000);
	$date = new DateTime( date('Y-m-d H:i:s.'.$micro,$cur_micro) );
	$_time = $date->format("Y-m-d H:i:s.u");
	
	$stmt = $dbh->prepare('INSERT INTO listing (name, time, ip) VALUES (:name, :time, :ip)');
	$stmt->execute(array(':name'=>$name, ':time'=>$_time, ':ip'=>$_SERVER['REMOTE_ADDR']));
	print 'ok';
}
?>
