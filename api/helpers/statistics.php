<?php
function getAttemptsCount($username) {
	global $dbh;
	global $env;

	$query = "SELECT count(name) AS amount
		FROM listing
		WHERE name = :name
			AND ISNULL(deleted)
	";
	if ($env['VITE_ENVIRONMENT'] == 'local') {
		$query = "SELECT count(name) AS amount
			FROM listing
			WHERE name = :name
				AND IFNULL(deleted, 1)
		";
	}
	$stmt = $dbh->prepare($query);
	$stmt->execute(array(':name' => $username));
	$result = $stmt->fetch();

	return $result['amount'];
}

function getCorrectAttemptsCount($username) {
	global $dbh;
	global $env;

	$query = "SELECT count(name) AS amount
		FROM listing
		WHERE name = :name
			AND (HOUR(time) = 13 AND MINUTE(time) = 37)
			AND ISNULL(deleted)
	";
	if ($env['VITE_ENVIRONMENT'] == 'local') {
		$query = "SELECT count(name) AS amount, time
			FROM listing
			WHERE name = :name
				AND (CAST(substr(time, 12, 2) as INTEGER) = 13 AND CAST(substr(time, 15, 2) as INTEGER) = 37)
				AND IFNULL(deleted, 1)
		";
	}
	$stmt = $dbh->prepare($query);
	$stmt->execute(array(':name' => $username));
	$result = $stmt->fetch();
	$resultCount = $stmt->fetchColumn();

	return $result['amount'];
}

function getBestAttempt($username) {
	global $dbh;
	global $env;

	$query = "SELECT time
		FROM listing
		WHERE name = :name
			AND (HOUR(time) = 13 AND MINUTE(time) = 37)
			AND ISNULL(deleted)
		ORDER BY SUBSTRING(time FROM 12) ASC
	";
	if ($env['VITE_ENVIRONMENT'] == 'local') {
		$query = "SELECT time
			FROM listing
			WHERE name = :name
				AND (CAST(substr(time, 12, 2) as INTEGER) = 13 AND CAST(substr(time, 15, 2) as INTEGER) = 37)
				AND IFNULL(deleted, 1)
			ORDER BY substr(time, 12) ASC
		";
	}
	$stmt = $dbh->prepare($query);
	$stmt->execute(array(':name' => $username));
	$result = $stmt->fetch();

	if (!$result) {
		return '';
	}
	return $result['time'];
}

function getLaatAttempt($username) {
	global $dbh;
	global $env;

	$query = "SELECT time
		FROM listing
		WHERE name = :name
			AND (HOUR(time) = 14 AND MINUTE(time) = 47)
			AND ISNULL(deleted)
		ORDER BY SUBSTRING(time FROM 12) ASC
	";
	if ($env['VITE_ENVIRONMENT'] == 'local') {
		$query = "SELECT time
			FROM listing
			WHERE name = :name
				AND (CAST(substr(time, 12, 2) as INTEGER) = 14 AND CAST(substr(time, 15, 2) as INTEGER) = 47)
				AND IFNULL(deleted, 1)
			ORDER BY substr(time, 12) ASC
		";
	}
	$stmt = $dbh->prepare($query);
	$stmt->execute(array(':name' => $username));
	$result = $stmt->fetch();

	if (!$result) {
		return '';
	}
	return $result['time'];
}

function getAverageTime($username) {
	global $dbh;
	global $env;

	$query = "SELECT SUBSTRING(time FROM 12) AS time
		FROM listing
		WHERE name = :name
			AND (HOUR(time) = 13 AND MINUTE(time) = 37)
			AND ISNULL(deleted)
	";
	if ($env['VITE_ENVIRONMENT'] == 'local') {
		$query = "SELECT substr(time, 12) AS time
			FROM listing
			WHERE name = :name
				AND (CAST(substr(time, 12, 2) as INTEGER) = 13 AND CAST(substr(time, 15, 2) as INTEGER) = 37)
				AND IFNULL(deleted, 1)
		";
	}
	$stmt = $dbh->prepare($query);
	$stmt->execute(array(':name' => $username));

	$total = 0;
	$count = 0;
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$total += intval(str_replace('.', '', str_replace(':', '', $row['time'])));
		$count++;
	}

	if ($count == 0) {
		return '';
	} else {
		$avg = round($total / $count);
		$result = substr($avg, 0, 2).':'.substr($avg, 2, 2).':'.substr($avg, 4, 2).'.'.substr($avg, 6);
	}
	return $result;
}

function getsAmountFirst($username) {
	global $dbh;
	global $env;

	$query = "SELECT count(name) AS amount_first
		FROM (
			SELECT name,
				DATE(time) AS date,
				SUBSTRING_INDEX(time, ' ', -1) AS moment
			FROM listing
			WHERE HOUR(time) = 13
				AND MINUTE(time) = 37
				AND ISNULL(deleted)
			GROUP BY DATE(time)
			ORDER BY DATE(time) DESC, moment ASC
		) AS top
		WHERE name = :name
	";
	if ($env['VITE_ENVIRONMENT'] == 'local') {
		$query = "SELECT count(name) AS amount_first
			FROM (
				SELECT name,
					DATE(time) AS date,
					SUBSTR(time, (INSTR(time, ' ')-1), (LENGTH(time)-INSTR(time, ' '))) AS moment
				FROM listing
				WHERE CAST(substr(time, 12, 2) as INTEGER) = 13
					AND CAST(substr(time, 15, 2) as INTEGER) = 37
					AND IFNULL(deleted, 1)
				GROUP BY DATE(time)
				ORDER BY DATE(time) DESC, moment ASC
			) AS top
			WHERE name = :name
		";
	}
	$stmt = $dbh->prepare($query);
	$stmt->execute(array(':name' => $username));
	$result = $stmt->fetch();

	return $result['amount_first'];
}

function getTopForDate($date) {
	global $dbh;
	global $env;

	$query = "SELECT name,
			DATE(time) AS date,
			SUBSTRING_INDEX(time, ' ', -1) AS moment
		FROM listing
		WHERE HOUR(time) = 13
			AND MINUTE(time) = 37
			AND ISNULL(deleted)
			AND DATE(time) = :date
		ORDER BY DATE(time) DESC, moment ASC
		LIMIT 3
	";
	if ($env['VITE_ENVIRONMENT'] == 'local') {
		$query = "SELECT name,
				DATE(time) AS date,
				SUBSTR(time, (INSTR(time, ' ')-1), (LENGTH(time)-INSTR(time, ' '))) AS moment
			FROM listing
			WHERE CAST(substr(time, 12, 2) as INTEGER) = 13
				AND CAST(substr(time, 15, 2) as INTEGER) = 37
				AND IFNULL(deleted, 1)
				AND DATE(time) = :date
			ORDER BY DATE(time) DESC, moment ASC
			LIMIT 3
		";
	}
	$stmt = $dbh->prepare($query);
	$stmt->execute(array(
		':date' => $date,
	));
	$result = $stmt->fetchAll();
	return $result;
}

function getUserTopStreak($username) {
	$result = [];
	$count = 0;
	$isStreakActive = true;
	while ($isStreakActive) {
		$result = getTopForDate(date('Y-m-d', strtotime('-'.$count.' days')));

		$existsInTop = false;
		foreach($result as $item) {
			if ($item['name'] == $username) {
				$existsInTop = true;
			}
		}
		if ($existsInTop) {
			$count++;
		} else {
			$isStreakActive = false;
		}
	}
	return $count;
}

function getUserStreak($username) {
	global $dbh;
	global $env;

	$query = "SELECT DATE(time) AS date
		FROM listing
		WHERE HOUR(time) = 13
			AND MINUTE(time) = 37
			AND ISNULL(deleted)
			AND name = :name
		GROUP BY DATE(time)
		ORDER BY DATE(time) ASC
	";
	if ($env['VITE_ENVIRONMENT'] == 'local') {
		$query = "SELECT DATE(time) AS date
			FROM listing
			WHERE CAST(substr(time, 12, 2) as INTEGER) = 13
				AND CAST(substr(time, 15, 2) as INTEGER) = 37
				AND IFNULL(deleted, 1)
				AND name = :name
			GROUP BY DATE(time)
			ORDER BY DATE(time) ASC
		";
	}
	$stmt = $dbh->prepare($query);
	$stmt->execute(array(
		':name' => $username,
	));
	$result = $stmt->fetchAll();

	$biggestStreak = 0;
	$currentStreak = 0;
	$previousDate = '';
	foreach ($result as $entry) {
		if (date_diff(new DateTime($entry['date']), new DateTime($previousDate))->format('%d') > 1) {
			if ($currentStreak > $biggestStreak) {
				$biggestStreak = $currentStreak;
			}
			$currentStreak = 1;
		} else {
			$currentStreak++;
		}
		$previousDate = $entry['date'];
	}
	if ($currentStreak > $biggestStreak) {
		$biggestStreak = $currentStreak;
	}

	return [
		'current' => $currentStreak,
		'longest' => $biggestStreak,
	];
}

function getUserStatistics($username) {
	$streaks = getUserStreak($username);
	/*
	 * Stats to add
	 * - Amount top 3
	 * - Amount 2nd
	 * - Amount 3rd
	 */
	return [
		"amount_first" => intval(getsAmountFirst($username)),
		// "amount_second" => -1,
		// "amount_third" => -1,
		"count" => intval(getAttemptsCount($username)),
		"count_on_time" => intval(getCorrectAttemptsCount($username)),
		"best" => getBestAttempt($username),
		"average" => getAverageTime($username),
		"current_streak" => $streaks['current'],
		"current_top_streak" => getUserTopStreak($username),
		"longest_streak" => $streaks['longest'],
		// "longest_top_streak" => -1,
	];
}
?>
