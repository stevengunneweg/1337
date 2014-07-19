<?php
/*
---- Achievements ----

1st of the day
2nd of the day
3rd of the day
2x 1st
5x 1st
1st of the week
1st of the month
1st of the year
1st all time
win by a milisec (max)
post in last second (13:37:59)
*/

include('db.php');

date_default_timezone_set("Europe/Amsterdam");
$dbh = null;

$achievements = {
	"1st": ""
}

$queryDay = $select." WHERE DATE(time) = CURDATE() ".$isLeet.$order;

try {
	$dbh = new PDO('mysql:host='.$host.';dbname='.$db_name.';port:3306', $username, $pass);
	if (isset($_POST['action'])) {
		switch($_POST['action']) {
			case 'getToday':
				getScore($queryDay);
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
?>
