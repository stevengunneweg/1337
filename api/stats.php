<?php
/*
---- Stats ----

amount of tries
times 1st
times 2nd
times 3rd
best winstreak
best attempt
current winstreak
average post time
*/

include('../db.php');

date_default_timezone_set("Europe/Amsterdam");
$dbh = null;
$name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);

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

$data = [
	"data" => [
		"posts" => [
			"count" => getTries(),
			"best" => getBestTry(),
			"average" => getAvgTime(),
		],
		"achievements" => [],
	],
];

header("Content-Type: application/json");
echo json_encode($data);
?>
