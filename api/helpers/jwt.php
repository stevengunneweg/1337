<?php
$jwtKey = "mock-key";

function _base64URLEncode($text) {
	return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
}

function _base64URLDecode($text) {
	return base64_decode(
		str_replace(
			["-", "_"],
			["+", "/"],
			$text
		)
	);
}

function jwtEncode($payload) {
	global $jwtKey;

	$header = json_encode([
		"alg" => "HS256",
		"typ" => "JWT"
	]);

	$header = _base64URLEncode($header);
	$payload = json_encode($payload);
	$payload = _base64URLEncode($payload);

	$signature = hash_hmac("sha256", $header . "." . $payload, $jwtKey, true);
	$signature = _base64URLEncode($signature);
	return $header . "." . $payload . "." . $signature;
}


function jwtDecode($token) {
	global $jwtKey;

	if (
		preg_match(
			"/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)$/",
			$token,
			$matches
		) !== 1
	) {
		throw new Exception("invalid token format");
	}

	$signature = hash_hmac(
		"sha256",
		$matches["header"] . "." . $matches["payload"],
		$jwtKey,
		true
	);
	$signature_from_token = _base64URLDecode($matches["signature"]);

	if (!hash_equals($signature, $signature_from_token)) {
		// throw new Exception("signature doesn't match");
		throw new Exception("signature doesn't match");
	}
	$payload = json_decode(_base64URLDecode($matches["payload"]), true);

	return $payload;
}
?>