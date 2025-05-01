<?php
$ACCOUNT_NAME_MIN_LENGTH = 5;
$ACCOUNT_PASSWORD_MIN_LENGTH = 8;

function hasAccount($email) {
	global $dbh;
	global $env;

	$isValidEmail = _isValidEmail($email);
	if ($isValidEmail['status'] == 'ok') {
		$statement = $dbh->prepare('SELECT id FROM accounts WHERE email = :email AND blocked <> 1 AND deleted <> 1');
		$statement->execute(array(
			':email' => $email,
		));
		$existingUser = $statement->fetch();
		if ($existingUser && $existingUser['id'] > 0) {
			return true;
		}
	}
	return false;
}

function getAccount($email) {
	global $dbh;
	global $env;
	global $ACCOUNT_NAME_MIN_LENGTH;

	$isValidEmail = _isValidEmail($email);
	if ($isValidEmail['status'] == 'ok') {
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
		}
	}
	return false;
}

function loginAccount($email, $password, $pepper) {
	global $dbh;
	global $env;
	global $ACCOUNT_NAME_MIN_LENGTH;
	global $ACCOUNT_PASSWORD_MIN_LENGTH;

	$isValidEmail = _isValidEmail($email);
	$isValidPassword = _isValidPassword($password);
	if ($isValidEmail['status'] == 'ok' && $isValidPassword['status'] == 'ok') {
		// Check if user exists
		$statement = $dbh->prepare('SELECT id, email, password, blocked, deleted FROM accounts WHERE email = :email AND blocked <> 1 AND deleted <> 1');
		$statement->execute(array(
			':email' => $email,
		));
		$existingUser = $statement->fetch();
		if ($existingUser && $existingUser['id'] > 0) {
			// Test password match
			if (password_verify($pepper._encodePassword($password), $existingUser['password'])) {
				echo "VAlid???";
				$statement = $dbh->prepare('UPDATE accounts SET last_login = :date WHERE id = :id');
				$statement->execute(array(
					':id' => $existingUser['id'],
					':date' => date('Y-m-d H:i:s'),
				));

				$jwtPayload = [
					"id" => $existingUser['id'],
					"email" => $existingUser["email"],
				];
				return [
					'token' => jwtEncode($jwtPayload),
				];
			}
		}
	}
	return "error";
}

function registerAccount($username, $email, $password, $pepper) {
	global $dbh;
	global $env;
	global $ACCOUNT_NAME_MIN_LENGTH;
	global $ACCOUNT_PASSWORD_MIN_LENGTH;

	$isValidName = _isValidUsername($username);
	$isValidEmail = _isValidEmail($email);
	$isValidPassword = _isValidPassword($password);
	if ($isValidName['status'] == 'ok' && $isValidEmail['status'] == 'ok' && $isValidPassword['status'] == 'ok') {
		if (hasAccount($email)) {
			return [
				'status' => 'error',
				'message' => 'account already exists',
			];
		}

		$hashedPassword = _hashPassword($password, $pepper);
		$statement = $dbh->prepare('INSERT INTO accounts (username, email, password, created_at, last_login) VALUES (:username, :email, :password, :date, :date)');
		$statement->execute(array(
			':username'=>$username,
			':email'=>$email,
			':password'=> $hashedPassword,
			':date' => date('Y-m-d H:i:s'),
		));
		return [
			'status' => 'ok',
		];
	} else if ($isValidName['code'] === 1001) {
		return [
			'status' => 'error',
			'message' => 'username is required',
		];
	} else if ($isValidName['code'] === 1002) {
		return [
			'status' => 'error',
			'message' => 'username must be at least '.$ACCOUNT_NAME_MIN_LENGTH.' characters long',
		];
	} else if ($isValidEmail['code'] === 2001) {
		return [
			'status' => 'error',
			'message' => 'email is required',
		];
	} else if ($isValidEmail['code'] === 2002) {
		return [
			'status' => 'error',
			'message' => 'email must be at least '.$ACCOUNT_PASSWORD_MIN_LENGTH.' characters long',
		];
	} else if ($isValidPassword['code'] === 3001) {
		return [
			'status' => 'error',
			'message' => 'password is required',
		];
	} else if ($isValidPassword['code'] === 3002) {
		return [
			'status' => 'error',
			'message' => 'password must be at least '.$ACCOUNT_PASSWORD_MIN_LENGTH.' characters long',
		];
	}
	return [
		'status' => 'error',
	];
}

function deleteAccount($email, $password, $pepper) {
	global $dbh;
	global $env;

	$isValidEmail = _isValidEmail($email);
	if ($isValidEmail['status'] == 'ok') {
		if (!hasAccount($email)) {
			return [
				'status' => 'error',
				'message' => 'account does not exist',
			];
		}

		$id = 1;
		if (password_verify($pepper._encodePassword($password), $existingUser['password'])) {
			$statement = $dbh->prepare('UPDATE accounts SET deleted = 1 WHERE id = :id');
			$statement->execute(array(
				':id' => $id,
			));
			return [
				'status' => 'ok',
			];
		}
	}
	return [
		'status' => 'error',
	];
}

function _encodePassword($password) {
	$hashedPassword = hash("sha512", $password);
	return $hashedPassword;
}

function _hashPassword($password, $pepper) {
	$hashedPassword = _encodePassword($password);
	return password_hash($pepper.$hashedPassword, PASSWORD_DEFAULT);
}

function _isValidUsername($username) {
	global $ACCOUNT_NAME_MIN_LENGTH;

	if (!$username) {
		return [
			'status' => 'error',
			'code' => 1001,
			'reason' => 'username is required',
		];
	} else if (strlen($username) < $ACCOUNT_NAME_MIN_LENGTH) {
		return [
			'status' => 'error',
			'code' => 1002,
			'reason' => 'username must be at least '.$ACCOUNT_NAME_MIN_LENGTH.' characters long',
		];
	}
	return [
		'status' => 'ok',
	];
}

function _isValidEmail($email) {
	global $ACCOUNT_NAME_MIN_LENGTH;

	if (!$email) {
		return [
			'status' => 'error',
			'code' => 2001,
			'reason' => 'email is required',
		];
	} else if (strlen($email) < $ACCOUNT_NAME_MIN_LENGTH) {
		return [
			'status' => 'error',
			'code' => 2002,
			'reason' => 'email must be at least '.$ACCOUNT_NAME_MIN_LENGTH.' characters long',
		];
	}
	return [
		'status' => 'ok',
	];
}

function _isValidPassword($password) {
	global $ACCOUNT_PASSWORD_MIN_LENGTH;

	if (!$password) {
		return [
			'status' => 'error',
			'code' => 3001,
			'reason' => 'password is required',
		];
	} else if (strlen($password) < $ACCOUNT_PASSWORD_MIN_LENGTH) {
		return [
			'status' => 'error',
			'code' => 3002,
			'reason' => 'password must be at least '.$ACCOUNT_PASSWORD_MIN_LENGTH.' characters long',
		];
	}
	return [
		'status' => 'ok',
	];
}
?>
