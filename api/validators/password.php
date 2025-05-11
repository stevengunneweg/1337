<?php
function _isValidPassword($password) {
	$minLength = 8;

	if (!($password ?? '')) {
		http_response_code(400);
		return [
			'status' => 'error',
			'code' => 3001,
			'message' => 'password is required',
		];
	} else if (strlen($password) < $minLength) {
		http_response_code(400);
		return [
			'status' => 'error',
			'code' => 3002,
			'message' => 'password must be at least '.$minLength.' characters long',
		];
	}
	return null;
}
?>