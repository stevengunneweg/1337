<?php
function hasAccount($email, $includeDeleted = false) {
	global $dbh;
	global $env;

	$deletedQ = ' AND blocked <> 1 AND deleted <> 1';
	if ($includeDeleted) {
		$deletedQ = '';
	}
	$statement = $dbh->prepare('SELECT id FROM accounts WHERE email = :email'.$deletedQ);
	$statement->execute(array(
		':email' => $email,
	));
	$existingUser = $statement->fetch();
	if ($existingUser && $existingUser['id'] > 0) {
		return true;
	}
	return false;
}

function hasAccountForUsername($username) {
	global $dbh;
	global $env;

	$statement = $dbh->prepare('SELECT id FROM accounts WHERE username = :username AND blocked <> 1 AND deleted <> 1');
	$statement->execute(array(
		':username' => $username,
	));
	$existingUser = $statement->fetch();
	if ($existingUser && $existingUser['id'] > 0) {
		return true;
	}
	return false;
}

function getAccount($email) {
	global $dbh;
	global $env;

	$statement = $dbh->prepare('SELECT id, username, email, created_at, last_login, blocked, deleted FROM accounts WHERE email = :email AND blocked <> 1 AND deleted <> 1');
	$statement->execute(array(
		':email' => $email,
	));
	$existingUser = $statement->fetch();
	if ($existingUser && $existingUser['id'] > 0) {
		return [
			'status' => 'ok',
			'username' => $existingUser['username'],
			'email' => $existingUser['email'],
			'id' => $existingUser['id'],
			'createdAt' => $existingUser['created_at'],
			'lastLogin' => $existingUser['last_login'],
		];
	} else {
		return [
			'status' => 'error',
			'message' => 'account does not exist',
		];
	}
}

function loginAccount($email, $password, $pepper) {
	global $dbh;
	global $env;

	// Check if user exists
	$statement = $dbh->prepare('SELECT id, email, password, blocked, deleted FROM accounts WHERE email = :email AND blocked <> 1 AND deleted <> 1');
	$statement->execute(array(
		':email' => $email,
	));
	$existingUser = $statement->fetch();
	if ($existingUser && $existingUser['id'] > 0) {
		// Test password match
		if (password_verify($pepper._encodePassword($password), $existingUser['password'])) {
			$statement = $dbh->prepare('UPDATE accounts SET last_login = :date WHERE id = :id');
			$statement->execute(array(
				':id' => $existingUser['id'],
				':date' => date('Y-m-d H:i:s'),
			));

			$jwtPayload = [
				"id" => $existingUser['id'],
				"email" => $existingUser["email"],
				"issue_date" => date('Y-m-d H:i:s'),
			];
			return [
				'token' => jwtEncode($jwtPayload),
			];
		}
	}
	http_response_code(401);
	return [
		'status' => 'error',
		'message' => 'account does not exist or password is incorrect',
	];
}

function registerAccount($username, $email, $password, $pepper) {
	global $dbh;
	global $env;

	if (hasAccount($email, true)) {
		http_response_code(400);
		return [
			'status' => 'error',
			'message' => 'account already exists',
		];
	}

	$hashedPassword = _hashPassword($password, $pepper);
	$statement = $dbh->prepare('INSERT INTO accounts (username, email, password, created_at, last_login) VALUES (:username, :email, :password, :date, :date)');
	try {
		$statement->execute(array(
			':username'=>$username,
			':email'=>$email,
			':password'=> $hashedPassword,
			':date' => date('Y-m-d H:i:s'),
		));
		return [
			'status' => 'ok',
		];
	} catch (Exception $e) {
		http_response_code(500);
		return [
			'status' => 'error',
			'message' => 'something went wrong',
		];
	}
}

function deleteAccount($email, $password, $pepper) {
	global $dbh;
	global $env;

	if (!hasAccount($email)) {
		return [
			'status' => 'error',
			'message' => 'account does not exist',
		];
	}
	$statement = $dbh->prepare('SELECT id, email, password, blocked, deleted FROM accounts WHERE email = :email AND blocked <> 1 AND deleted <> 1');
	$statement->execute(array(
		':email' => $email,
	));
	$existingUser = $statement->fetch();
	if (password_verify($pepper._encodePassword($password), $existingUser['password'])) {
		$statement = $dbh->prepare('UPDATE accounts SET deleted = 1 WHERE id = :id');
		$statement->execute(array(
			':id' => $existingUser['id'],
		));
		return [
			'status' => 'ok',
		];
	}
}

function updateUsername($username, $email) {
	global $dbh;
	global $env;

	$statement = $dbh->prepare('UPDATE  accounts SET username = :username WHERE email = :email');
	try {
		$statement->execute(array(
			':username' => $username,
			':email' => $email,
		));
		return [
			'status' => 'ok',
		];
	} catch (Exception $e) {
		http_response_code(500);
		return [
			'status' => 'error',
			'message' => 'something went wrong',
		];
	}
}

function getAvatarUrl($email) {
	$hashedEmail = hash("sha512", strtolower(trim($email)));
	return 'https://api.gravatar.com/avatar/'.$hashedEmail;
}

function _encodePassword($password) {
	$hashedPassword = hash("sha512", $password);
	return $hashedPassword;
}

function _hashPassword($password, $pepper) {
	$hashedPassword = _encodePassword($password);
	return password_hash($pepper.$hashedPassword, PASSWORD_DEFAULT);
}
?>
