<?php

header('Content-Type: text/plain; charset=UTF-8');

echo "Kommaforslag at commit e94617b44d68fcb7e04b66173a9caa8da18456b8 (r174) deployed on 2018-04-04 14:37:48\n\n";

echo "uptime:\n", shell_exec('uptime'), "\n";
echo "free -h:\n", shell_exec('free -h'), "\n";
// echo "df -h:\n", shell_exec('df -h'), "\n";
