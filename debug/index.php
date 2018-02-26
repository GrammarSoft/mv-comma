<?php

header('Content-Type: text/plain; charset=UTF-8');

echo "Kommaforslag at commit 55a9c53c77204dc81c5f83ca02179ccdd4afbd02 (r169) deployed on 2018-02-26 13:26:54\n\n";

echo "uptime:\n", shell_exec('uptime'), "\n";
echo "free -h:\n", shell_exec('free -h'), "\n";
// echo "df -h:\n", shell_exec('df -h'), "\n";
