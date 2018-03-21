<?php

require_once __DIR__.'/config.php';

function json_encode_num($v) {
	return json_encode($v, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

function hmac_sha256_b64($data, $secret) {
	return trim(base64_encode(hash_hmac('sha256', $data, $secret, true)), '=');
}

function mvid_keepalive($mv_session_id) {
	if (empty($mv_session_id)) {
		return false;
	}

	$stamp = time();

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://mvidsignonapi.vitec-mv.com/keepalive');
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Accept: application/json',
		'SessionID: '.$mv_session_id,
		'RequestDateTime: '.$stamp,
		]);
	$rv = curl_exec($ch);
	$e = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if ($e < 200 || $e >= 400) {
		return false;
	}
	return true;
}

function mvid_check_access($mv_session_id) {
	if (empty($mv_session_id)) {
		return false;
	}

	$service = $GLOBALS['-config']['MVID_SERVICE'];
	$secret = $GLOBALS['-config']['MVID_SECRET'];

	if (!empty($GLOBALS['access-hmac'])) {
		$data = json_decode($GLOBALS['access-hmac'], true);
		if (!empty($data['c']) && !empty($data['s']) && !empty($data['h']) && $data['c'] === $GLOBALS['-config']['HMAC_SERVICE'] && $data['s'] >= time()) {
			$hmac = hmac_sha256_b64("{$data['c']}-{$data['s']}-{$mv_session_id}", $secret);
			if ($hmac === $data['h']) {
				return true;
			}
		}
	}

	$uri = 'https://mvidsignonapi.vitec-mv.com/accessidentifiers';
	$stamp = time();
	$body = json_encode_num($GLOBALS['-config']['MVID_ACCESS_IDS']);
	$hash = base64_encode(hash_hmac('sha256', "{$body}PUT{$uri}{$stamp}", $secret, true));

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $uri);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Accept: application/json',
		'Content-Type: application/json-patch+json',
		'Content-Length: '.strlen($body),
		'SessionID: '.$mv_session_id,
		'Authorization: '.$service.':'.$hash,
		'RequestDateTime: '.$stamp,
		]);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
	$rv = curl_exec($ch);
	$e = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if ($e < 200 || $e >= 400) {
		return false;
	}

	if (empty($rv)) {
		return false;
	}

	$rv = json_decode($rv, true);
	if (empty($rv)) {
		return false;
	}

	$found = false;
	foreach ($rv as $r) {
		if (in_array($r['AI'], $GLOBALS['-config']['MVID_ACCESS_IDS'])) {
			$found = true;
			break;
		}
	}

	if (!$found) {
		return false;
	}

	$data = ['s' => time() + 11*60, 'c' => $GLOBALS['-config']['HMAC_SERVICE']];
	$data['h'] = hmac_sha256_b64("{$data['c']}-{$data['s']}-{$mv_session_id}", $secret);
	$GLOBALS['access-hmac'] = json_encode_num($data);
	setcookie('access-hmac', $GLOBALS['access-hmac'], time() + 10*60);
	$GLOBALS['hmac-fresh'] = true;
	return true;
}

$GLOBALS['mv-session-id'] = '';
if (!empty($_REQUEST['SessionID'])) {
	$GLOBALS['mv-session-id'] = $_REQUEST['SessionID'];
}
else if (!empty($_COOKIE['mv-session-id'])) {
	$GLOBALS['mv-session-id'] = $_COOKIE['mv-session-id'];
}

$GLOBALS['hmac-fresh'] = false;
$GLOBALS['access-hmac'] = '';
if (!empty($_COOKIE['access-hmac'])) {
	$GLOBALS['access-hmac'] = $_COOKIE['access-hmac'];
}
else if (!empty($_SERVER['HTTP_HMAC'])) {
	$GLOBALS['access-hmac'] = $_SERVER['HTTP_HMAC'];
}

if (!empty($GLOBALS['mv-session-id'])) {
	setcookie('mv-session-id', $GLOBALS['mv-session-id'], time()+60*60*24*7, '/', '', true);
}
