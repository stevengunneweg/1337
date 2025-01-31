<?php
function getLeaderboard($span = 'today') {
	global $dbh;
	global $env;

	$select = "SELECT name, time, SUBSTRING_INDEX(time, ' ', 1) AS day, SUBSTRING_INDEX(time, ' ', -1) AS moment FROM listing ";
	$isLeet = " AND (HOUR(time) = 13 AND MINUTE(time) = 37) ";
	$order = " ORDER BY moment ASC LIMIT 30";

	$queryDay = $select." WHERE deleted IS NULL AND DATE(time) = CURDATE() ".$isLeet.$order;
	$queryYesterday = $select." WHERE deleted IS NULL AND DATE(time) = DATE_ADD(CURDATE(), INTERVAL -1 DAY) ".$isLeet.$order;
	$queryWeek = $select." WHERE deleted IS NULL AND WEEKOFYEAR(time) = WEEKOFYEAR(CURDATE()) AND YEAR(time) = YEAR(CURDATE()) ".$isLeet.$order;
	$queryMonth = $select." WHERE deleted IS NULL AND MONTH(time) = MONTH(CURDATE()) AND YEAR(time) = YEAR(CURDATE()) ".$isLeet.$order;
	$queryYear = $select." WHERE deleted IS NULL AND YEAR(time) = YEAR(CURDATE()) ".$isLeet.$order;
	$queryTop = $select."WHERE deleted IS NULL AND true".$isLeet.$order;

	if ($env['VITE_ENVIRONMENT'] == 'local') {
		$select = "SELECT name, time, date(time) as day, substr(time, 12) as moment FROM listing ";
		$isLeet = " AND ((CAST(substr(time, 12, 2) as INTEGER) = 13 AND CAST(substr(time, 15, 2) as INTEGER) = 37)) ";

		$queryDay = $select." WHERE deleted IS NULL AND date(time) = date('now') ".$isLeet.$order;
		$queryYesterday = $select." WHERE deleted IS NULL AND date(time) = date('now','-1 day') ".$isLeet.$order;
		$queryWeek = $select." WHERE deleted IS NULL AND strftime('%W', time) = strftime('%W', date('now')) AND strftime('%Y', time) = strftime('%Y', date('now')) ".$isLeet.$order;
		$queryMonth = $select." WHERE deleted IS NULL AND strftime('%m', time) = strftime('%m', date('now')) AND strftime('%Y', time) = strftime('%Y', date('now')) ".$isLeet.$order;
		$queryYear = $select." WHERE deleted IS NULL AND strftime('%Y', time) = strftime('%Y', date('now')) ".$isLeet.$order;
		$queryTop = $select." WHERE deleted IS NULL AND true ".$isLeet.$order;
	}

	$query = $queryDay;
	switch ($span) {
		case 'today':
			$query = $queryDay;
			break;
		case 'yesterday':
			$query = $queryYesterday;
			break;
		case 'week':
			$query = $queryWeek;
			break;
		case 'month':
			$query = $queryMonth;
			break;
		case 'year':
			$query = $queryYear;
			break;
		case 'top':
			$query = $queryTop;
			break;
	}

	$stmt = $dbh->prepare($query);
	$stmt->execute();
	$result = $stmt->fetchAll();
	return $result;
}

function getLeaderboardToday() {
	return getLeaderboard('today');
}
function getLeaderboardYesterday() {
	return getLeaderboard('yesterday');
}
function getLeaderboardWeek() {
	return getLeaderboard('week');
}
function getLeaderboardMonth() {
	return getLeaderboard('month');
}
function getLeaderboardYear() {
	return getLeaderboard('year');
}
function getLeaderboardTop() {
	return getLeaderboard('top');
}

function hasRecordToday($ip) {
	global $dbh;
	global $env;

	if ($env['VITE_ENVIRONMENT'] == 'local') {
		$stmt = $dbh->prepare("SELECT count(ip) as ip_count, time, time(time) as timeRecord, time('now', 'localtime', '-20 seconds') as timeOffset FROM listing WHERE date(time) = date('now') AND timeRecord > timeOffset AND ip = '".$ip."'");
	} else {
		$stmt = $dbh->prepare("SELECT count(ip) as ip_count FROM listing WHERE DATE(time) = CURDATE() AND TIME_TO_SEC(time) > TIME_TO_SEC(CURTIME()) - 20 AND ip = '".$ip."'");
	}
	$stmt->execute();
	$result = $stmt->fetchAll();

	return ($result[0]['ip_count'] > 0);
}

function registerPost($name) {
	global $isDevelop;
	global $dbh;

	if (strlen($name) > 20) {
		return ['status' => 'Name is too long'];
	} else if (strlen($name) < 2) {
		return ['status' => 'Name is too short'];
	} else if (strpos($_SERVER['HTTP_REFERER'], '1337online.com') == false && (!$isDevelop || strpos($_SERVER['HTTP_REFERER'], 'localhost') == false)) {
		return ['status' => 'You sneaky boy...'];
	} else if (hasRecordToday($_SERVER['REMOTE_ADDR'])) {
		return ['status' => 'You need to wait 20 after your previous post'];
	}

	$_time = getCurrentServerTime();

	// $_time = '2025-01-17 13:37:01.598051';

	$stmt = $dbh->prepare('INSERT INTO listing (name, time, ip) VALUES (:name, :time, :ip)');
	$stmt->execute(array(':name'=>$name, ':time'=>$_time, ':ip'=>$_SERVER['REMOTE_ADDR']));
	return [
		'status' => 'ok',
		'time' => substr($_time, 11),
	];
}
?>
