<?php
function _isValidEmail($email) {
	$minLength = 3;

	if (!($email ?? '')) {
		http_response_code(400);
		return [
			'status' => 'error',
			'code' => 2001,
			'message' => 'email is required',
		];
	} else if (strlen($email) < $minLength) {
		http_response_code(400);
		return [
			'status' => 'error',
			'code' => 2002,
			'message' => 'email must be at least '.$minLength.' characters long',
		];
	}
	return null;
}
?>