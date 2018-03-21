<?php

header('Content-Type: text/plain; charset=UTF-8');

echo "Kommaforslag at commit 57413239d64f569233e358d36b302132eb1a692e (r170) deployed on 2018-03-21 09:23:00\n\n";

echo "uptime:\n", shell_exec('uptime'), "\n";
echo "free -h:\n", shell_exec('free -h'), "\n";
// echo "df -h:\n", shell_exec('df -h'), "\n";
