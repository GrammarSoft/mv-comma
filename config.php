<?php

$vars = ['DEBUG_KEY', 'COMMA_HOST', 'COMMA_PORT', 'DANPROOF_URL', 'MVID_SERVICE', 'MVID_SECRET', 'MVID_ACCESS_IDS', 'GOOGLE_AID'];
foreach ($vars as $var) {
	$env = getenv($var);
	if (empty($_ENV[$var])) {
		$_ENV[$var] = empty($env) ? null : $env;
	}
}

$GLOBALS['-config'] = [];
$GLOBALS['-config']['DEBUG_KEY'] = $_ENV['DEBUG_KEY'];
$GLOBALS['-config']['COMMA_HOST'] = $_ENV['COMMA_HOST'] ?? 'localhost';
$GLOBALS['-config']['COMMA_PORT'] = $_ENV['COMMA_PORT'] ?? 13300;
$GLOBALS['-config']['DANPROOF_URL'] = $_ENV['DANPROOF_URL'];
$GLOBALS['-config']['MVID_SERVICE'] = $_ENV['MVID_SERVICE'] ?? 'kommaforslag';
$GLOBALS['-config']['MVID_SECRET'] = $_ENV['MVID_SECRET'];
$GLOBALS['-config']['MVID_ACCESS_IDS'] = $_ENV['MVID_ACCESS_IDS'] ?? 'product.web.da.commasuggestions.release';
$GLOBALS['-config']['GOOGLE_AID'] = $_ENV['GOOGLE_AID'];

$GLOBALS['-config']['MVID_ACCESS_IDS'] = explode(',', trim(preg_replace('[,+]', ',', preg_replace('~[\s\r\n\t]+~', ',', $GLOBALS['-config']['MVID_ACCESS_IDS'])), ','));
