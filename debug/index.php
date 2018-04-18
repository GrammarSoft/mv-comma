<?php

header('Content-Type: text/plain; charset=UTF-8');

echo "Kommaforslag at commit b4f9e87faf3e979ceb0dc5f4a3dac86b29c957e1 (r175) deployed on 2018-04-18 12:53:32\n\n";

echo "uptime:\n", shell_exec('uptime'), "\n";
echo "free -h:\n", shell_exec('free -h'), "\n";
// echo "df -h:\n", shell_exec('df -h'), "\n";
