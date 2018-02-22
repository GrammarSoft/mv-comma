<?php

header('Content-Type: text/plain; charset=UTF-8');

echo "Kommaforslag at commit 283f5c5f1d9efe56d699d04011f099c042618181 (r167) deployed on 2018-02-22 15:38:59\n\n";

echo "uptime:\n", shell_exec('uptime'), "\n";
echo "free -h:\n", shell_exec('free -h'), "\n";
// echo "df -h:\n", shell_exec('df -h'), "\n";
