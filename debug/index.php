<?php

header('Content-Type: text/plain; charset=UTF-8');

echo "Kommaforslag at commit 0e94f44a400e54fa91c4f91b324e6d505566c311 (r172) deployed on 2018-04-04 10:17:00\n\n";

echo "uptime:\n", shell_exec('uptime'), "\n";
echo "free -h:\n", shell_exec('free -h'), "\n";
// echo "df -h:\n", shell_exec('df -h'), "\n";
