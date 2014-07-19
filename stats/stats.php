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

/*include('db.php');

date_default_timezone_set("Europe/Amsterdam");
$dbh = null;

$tries = "SELECT count(name) FROM listing WHERE name = :name";

try {
	$dbh = new PDO('mysql:host='.$host.';dbname='.$db_name.';port:3306', $username, $pass);
	if (isset($_POST['action'])) {
		switch($_POST['action']) {
			case 'tries':
				getScore($tries);
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
}*/
?>

<!DOCTYPE html>
<html>
<head>
	<title>1337 Stats</title>
	<style type="text/css">
		body, html {
			margin: 0px;
			padding: 0px;
		}
		#stats, #achievements {
			float: left;
		}
	</style>
</head>
<body>
	<h1>Name</h1>
	<div id="stats"><h2>Stats</h2></div>
	<div id="achievements"><h2>Achievements</h2></div>
</body>
</html>
