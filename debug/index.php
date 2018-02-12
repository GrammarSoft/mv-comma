<?php

header('Content-Type: text/plain; charset=UTF-8');

echo "Kommaforslag at commit 605f6bd961ae7e98964df23f722d66cfe5f7fb70 (r165) deployed on 2018-02-12 15:23:59\n\n";

echo "uptime:\n", shell_exec('uptime'), "\n";
echo "free -h:\n", shell_exec('free -h'), "\n";
// echo "df -h:\n", shell_exec('df -h'), "\n";
