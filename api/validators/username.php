<?php
function _isValidUsername($username) {
	$minLength = 3;
	$maxLength = 20;

	if (!($username ?? '')) {
		http_response_code(400);
		return [
			'status' => 'error',
			'code' => 2001,
			'message' => 'username is required',
		];
	} else if (strlen($username) < $minLength) {
		http_response_code(400);
		return [
			'status' => 'error',
			'code' => 2002,
			'message' => 'username must be at least '.$minLength.' characters long',
		];
	} else if (strlen($username) > $maxLength) {
		http_response_code(400);
		return [
			'status' => 'error',
			'code' => 2002,
			'message' => 'username can not be longer than '.$maxLength.' characters',
		];
	}
	return null;
}
?>