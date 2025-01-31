<?php
function isV1User($username) {
	global $dbh;
	global $env;

	$query = "SELECT count(name) AS amount
		FROM listing
		WHERE name = :name
			AND ISNULL(deleted)
			AND DATE(time) BETWEEN '2000-01-01' AND '2021-02-01'
	";
	if ($env['VITE_ENVIRONMENT'] == 'local') {
		$query = "SELECT count(name) AS amount
			FROM listing
			WHERE name = :name
				AND IFNULL(deleted, 1)
				AND DATE(time) BETWEEN '2000-01-01' AND '2021-02-01'
		";
	}
	$stmt = $dbh->prepare($query);
	$stmt->execute(array(':name' => $username));
	$result = $stmt->fetch();

	return $result['amount'] > 0;
}

function isV2User($username) {
	global $dbh;
	global $env;

	$query = "SELECT count(name) AS amount
		FROM listing
		WHERE name = :name
			AND ISNULL(deleted)
			AND DATE(time) BETWEEN '2021-02-01' AND '2025-02-01'
	";
	if ($env['VITE_ENVIRONMENT'] == 'local') {
		$query = "SELECT count(name) AS amount
			FROM listing
			WHERE name = :name
				AND IFNULL(deleted, 1)
				AND DATE(time) BETWEEN '2021-02-01' AND '2025-02-01'
		";
	}
	$stmt = $dbh->prepare($query);
	$stmt->execute(array(':name' => $username));
	$result = $stmt->fetch();

	return $result['amount'] > 0;
}

function getUserAchievements($username) {
	/*
	* Achievements to add
	* - First
	* - Second (or first)
	* - Third (or second or first)
	* - V1 user
	* - V2 user
	* - 3 day streak
	* - 5 day streak
	* - 10 day streak
	* - 14:47 (late)
	*/
	$userStreak = getUserStreak($username)['longest'];

	return [
		'first' => intval(getsAmountFirst($username)) > 0,
		'second' => false,
		'third' => false,
		'v1_user' => isV1User($username),
		'v2_user' => isV2User($username),
		'streak_3' => $userStreak >= 3,
		'streak_5' => $userStreak >= 5,
		'streak_10' => $userStreak >= 10,
		'late' => getLaatAttempt($username) != '',
	];
}
?>
