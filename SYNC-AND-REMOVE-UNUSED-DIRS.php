<?php

$SYNC_BASE = '/dev/shm/wp/';

if(!is_dir($SYNC_BASE)) mkdir($SYNC_BASE,0755, true);

function RUN_AT_DIR($DIR) {
    echo $DIR . PHP_EOL;
}

foreach(glob('/data/*/') as $DIR) {
     RUN_AT_DIR($DIR);
}
