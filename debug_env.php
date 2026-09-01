<?php
$lines = file('c:/Users/siril/Downloads/89999/leave-management/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $l) {
    $parts = explode('=', $l, 2);
    $k = trim($parts[0]);
    $v = isset($parts[1]) ? trim($parts[1]) : '';
    echo $k . '=[' . $v . '] bytes=' . strlen($v) . PHP_EOL;
}
