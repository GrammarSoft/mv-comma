<?php
$start = microtime(true);

ob_start();
require_once __DIR__.'/config.php';
if (empty($_REQUEST['key']) || $_REQUEST['key'] !== $GLOBALS['-config']['DEBUG_KEY']) {
	header('HTTP/1.1 403 DDoS Protection');
	die();
}

// Assume everything is going to hell, until we can prove otherwise
header('HTTP/1.1 500 Failed Selftest');
header('Content-Type: text/plain; charset=UTF-8');

$tests = [
	"<s1>\nEn sætning der mangler komma.\n</s1>" => "<s1>
En
sætning
der	%ko-FSstart
mangler
komma
.
</s1>",
	"<s1>\nEn hund der mangler komma.\n</s1>" => "<s1>
En
hund
der	%ko-FSstart
mangler
komma
.
</s1>",
	"<s1>\nEt hus der mangler komma.\n</s1>" => "<s1>
Et
hus
der	%ko-FSstart
mangler
komma
.
</s1>",
];

$k = array_rand($tests);

$port = $GLOBALS['-config']['COMMA_PORT'];
$s = fsockopen($GLOBALS['-config']['COMMA_HOST'], $port);
fwrite($s, $k."\n<END-OF-INPUT>\n");
$out = stream_get_contents($s);
$out = trim($out);

$stop = round(microtime(true) - $start, 3);
if ($out === $tests[$k]) {
	header('HTTP/1.1 200 OK');
	echo "Selftest passed ({$stop}s)";
	die();
}
else {
	echo "Selftest failed ({$stop}s): '{$out}' !== '{$tests[$k]}'";
}
