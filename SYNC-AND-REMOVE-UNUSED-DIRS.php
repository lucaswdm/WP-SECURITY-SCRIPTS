<?php

$SYNC_BASE = '/dev/shm/wp/';

if(!is_dir($SYNC_BASE)) mkdir($SYNC_BASE,0755, true);

function RUN_AT_DIR($DIR) {
    echo $DIR . PHP_EOL;
}

foreach(glob('/data/*/') as $DIR) {
    $DOMAIN = basename($DIR);
     if( !validateDomain($DOMAIN) ||  strtolower($DIR) != $DIR || !is_dir($DIR)) continue;
     RUN_AT_DIR($DIR);
}









function validateDomain($domain_name)
{
    if(filter_var($domain_name, FILTER_VALIDATE_IP)) return false;
    if(strpos($domain_name, '.') === false) return false;
    return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
            && preg_match("/^.{1,253}$/", $domain_name) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label
}
