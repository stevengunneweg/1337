<?php
function getActiveUsers() {
	global $dbh;
	global $env;

	// Calculate time offset
	$date = new DateTime(date('Y-m-d H:i:s'));
	$interval = new DateInterval("PT3S"); // 3 seconds
	$interval->invert = 1;
	$date->add($interval);

	$name = filter_input(INPUT_GET, 'name', FILTER_UNSAFE_RAW);
	$_timeOffset = $date->format('Y-m-d H:i:s');
	$_time = getCurrentServerTime();
	$_ip = $_SERVER['REMOTE_ADDR'];

	if ($name) {
		// check if user (name and ip) already exists
		$statement = $dbh->prepare('SELECT id, username, ip FROM active_users WHERE ip = :ip');
		$statement->execute(array(':ip'=>$_ip));
		$existingUser = $statement->fetch();
		if ($existingUser && $existingUser['id'] > 0) {
			$stmt = $dbh->prepare('UPDATE active_users SET time=:time, username=:name WHERE id=:id');
			$stmt->execute(array(':time'=>$_time, ':name'=>$name, ':id'=>$existingUser['id']));
		} else {
			$stmt = $dbh->prepare('INSERT INTO active_users (username, time, ip) VALUES (:name, :time, :ip)');
			$stmt->execute(array(':name'=>$name, ':time'=>$_time, ':ip'=>$_ip));
		}
	}

	// get amount of active users
	$statement = $dbh->prepare('SELECT DISTINCT username, time FROM active_users WHERE time >= :time ORDER BY time DESC');
	$statement->execute(array(':time'=>$_timeOffset));
	$result = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
	$activeUsers = $result;
	return $activeUsers;
}
?>
