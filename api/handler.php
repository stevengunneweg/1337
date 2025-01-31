<?php
$env = parse_ini_file('../.env');
if ($env['VITE_ENVIRONMENT'] == 'prod') {
	$isDevelop = false;
} else {
	$isDevelop = true;
}

if ($isDevelop) {
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);
}

try {
	include('../db.php');

	if ($env['VITE_ENVIRONMENT'] == 'local') {
		$dbh = new PDO('sqlite:mock-db.sqlite.db');
	} else {
		$dbh = new PDO('mysql:host='.$host.';dbname='.$db_name.';port:3306', $username, $pass);
	}
} catch (PDOException $e) {
	print 'Error connecting to database';
	die();
}

date_default_timezone_set('Europe/Amsterdam');

include('./helpers/time.php');
include('./helpers/users.php');
include('./helpers/leaderboard.php');
include('./helpers/statistics.php');
include('./helpers/achievements.php');

$activeUsers = getActiveUsers();

// Handle CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	if ($isDevelop) {
		header('Access-Control-Allow-Origin: *');
	} else {
		header('Access-Control-Allow-Origin: https://1337online.com,https://dev.1337online.com');
	}
	header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, OPTIONS');
	header('Access-Control-Allow-Headers: Authorization, Content-Type, Accept, Origin');
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Max-Age: 86400');
	header('Content-Type: application/json');
	print json_encode([]);
	exit(0);
}

if ($isDevelop) {
	header('Access-Control-Allow-Origin: *');
} else {
	header('Access-Control-Allow-Origin: https://1337online.com,https://dev.1337online.com');
}
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

switch (filter_input(INPUT_GET, 'action', FILTER_UNSAFE_RAW)) {
	case 'status':
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			print json_encode([
				'data' => [
					'status' => 'alive',
				]
			]);
		} else {
			http_response_code(405);
			print json_encode([
				'data' => 'Method not allowed',
			]);
		}
		break;
	case 'time':
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			print json_encode([
				'data' => [
					'time' => getCurrentServerTime(),
				],
			]);
		} else {
			http_response_code(405);
			print json_encode([
				'data' => 'Method not allowed',
			]);
		}
		break;
	case 'users':
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			print json_encode([
				'data' => [
					'activeUsers' => $activeUsers,
				],
			]);
		} else {
			http_response_code(405);
			print json_encode([
				'data' => 'Method not allowed',
			]);
		}
		break;
	case 'leaderboard':
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			$leaderboard = [];
			switch (filter_input(INPUT_GET, 'period', FILTER_UNSAFE_RAW)) {
				case 'yesterday':
					$leaderboard = getLeaderboardYesterday();
					break;
				case 'week':
					$leaderboard = getLeaderboardWeek();
					break;
				case 'month':
					$leaderboard = getLeaderboardMonth();
					break;
				case 'year':
					$leaderboard = getLeaderboardYear();
					break;
				case 'top':
					$leaderboard = getLeaderboardTop();
					break;
				default:
					$leaderboard = getLeaderboard();
			}
			print json_encode([
				'data' => $leaderboard,
			]);
		} else {
			http_response_code(405);
			print json_encode([
				'data' => 'Method not allowed',
			]);
		}
		break;
	case 'post':
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$data = json_decode(file_get_contents('php://input'), true);
			$name = htmlspecialchars($data['name']);
			$result = registerPost($name);

			if ($result['status'] !== 'ok') {
				http_response_code(406);
				print json_encode([
					'data' => $result,
				]);
				exit(0);
			}

			print json_encode([
				'data' => [
					'status' => 'Your post was registered',
					'time' => $result['time'],
				],
			]);
		} else {
			http_response_code(405);
			print json_encode([
				'data' => ['status' => 'Method not allowed'],
			]);
		}
		break;
	case 'stats':
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			$username = filter_input(INPUT_GET, 'user', FILTER_UNSAFE_RAW);
			$stats = getUserStatistics($username);
			$achievements = getUserAchievements($username);

			print json_encode([
				'data' => [
					'stats' => $stats,
					'achievements' => $achievements,
				],
			]);
		} else {
			http_response_code(405);
			print json_encode([
				'data' => 'Method not allowed',
			]);
		}
		break;
	case 'test':
		$username = filter_input(INPUT_GET, 'user', FILTER_UNSAFE_RAW);
		$result = [];

		$result = getUserAchievements($username);
	// 	global $dbh;

	// 	try {
	// 		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// 		$query = "show tables";
	// 		$stmt = $dbh->prepare($query);
	// 		$stmt->execute(array(
	// 			':name' => $username,
	// 		));
	// 		$result = $stmt->fetchAll();
	// 	} catch (PDOException $e) {
	// 		echo 'Connection failed: ' . $e->getMessage();
	// 	}

		print json_encode([
			'data' => $result,
		]);

		break;
	default:
		http_response_code(404);
		print json_encode([
			'data' => 'Endpoint not found',
		]);
}
?>
