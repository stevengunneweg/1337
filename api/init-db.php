<?php
$env = parse_ini_file('../.env');

try {
	include('../db.php');
	$dbh = new PDO('sqlite:mock-db.sqlite.db');
} catch (PDOException $e) {
	print 'Error connecting to database';
	die();
}

// Setup "listing"
if ($env['VITE_ENVIRONMENT'] == 'local') {
	$dbh->prepare('CREATE TABLE IF NOT EXISTS listing (
		id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
		name TEXT,
		time TEXT,
		ip TEXT,
		deleted INTEGER
	)')->execute();
} else {
	$dbh->prepare('CREATE TABLE IF NOT EXISTS listing (
		id INT PRIMARY KEY AUTOINCREMENT NOT NULL,
		name VARCHAR(255),
		time VARCHAR(255),
		ip VARCHAR(128),
		deleted BOOLEAN
	)')->execute();
}

// Setup "active_users"
if ($env['VITE_ENVIRONMENT'] == 'local') {
	$dbh->prepare('CREATE TABLE IF NOT EXISTS "active_users" (
		"id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
		"username" TEXT,
		"time" TEXT,
		"ip" TEXT
	)')->execute();
} else {
	$dbh->prepare('CREATE TABLE IF NOT EXISTS active_users (
		id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
		username VARCHAR(255),
		time VARCHAR(255),
		ip VARCHAR(128)
	)')->execute();
}
?>
