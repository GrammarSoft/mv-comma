<?php

header('Content-Type: text/plain; charset=UTF-8');

echo "Kommaforslag at commit cb5d2613c6767532658a4e5fbcb5e29d5ee06d70 (r163) deployed on 2018-02-09 13:51:07\n\n";

echo "uptime:\n", shell_exec('uptime'), "\n";
echo "free -h:\n", shell_exec('free -h'), "\n";
// echo "df -h:\n", shell_exec('df -h'), "\n";
