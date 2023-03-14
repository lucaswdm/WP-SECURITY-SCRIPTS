<?php

@mkdir('/dev/shm/wp/', 0777, true);
@system("cd /dev/shm/wp/ && wp-cli core download --path=/dev/shm/wp/ --allow-root");

if(!is_dir('/dev/shm/wp/') || !is_dir('/dev/shm/wp/wp-includes')) {
    exit('/dev/shm/wp/ #404'); 
}

#exit;

$WHITELIST = [];

$WHITELISTED_PHPS = [
    'index.php',
    'wp-config.php',
    'wp-settings.php',
    '__prepend.inc.php',
];

$WHITELISTED_DIRS = [
    'wp-content',
    'wp-admin',
    'wp-includes',
];

foreach(glob('/dev/shm/wp/*.php') as $file) {
    $WHITELISTED_PHPS[] = basename($file);
}

$PREPEND = file_get_contents('https://raw.githubusercontent.com/lucaswdm/isoladorcentosphpfpm/master/__prepend.inc.php');

if(strlen($PREPEND) < 1024) exit('Error: __prepend.inc.php');

#echo $PREPEND; exit;

$FLG_SYNC = true;
$FLG_IDENTIFY_SUSPECTEDS_PHPS = true;

foreach(glob('/data/*/') as $dir)
{
    if(is_dir($dir . 'wp-includes/'))
    {

        file_put_contents($dir . '__prepend.inc.php', $PREPEND);

        $DOMAIN = basename($dir);
        if(!in_array($DOMAIN, $WHITELIST)) {
            #continue;
        }

        #sleep(1);

        if($FLG_SYNC){

        $SHELL = "rsync -avz /dev/shm/wp/ " . $dir;
        echo $SHELL . PHP_EOL;
        system($SHELL);

        $SHELL = "rsync -avz /dev/shm/wp/wp-includes/ " . $dir . "wp-includes/ --delete";
        echo $SHELL . PHP_EOL;
        system($SHELL);

        $SHELL = "rsync -avz /dev/shm/wp/wp-admin/ " . $dir . "wp-admin/ --delete";
        echo $SHELL . PHP_EOL;
        system($SHELL);

    }

        $FILES_PHP = glob($dir . '*.php');

        foreach($FILES_PHP as $filephp)
        {
            if($FLG_IDENTIFY_SUSPECTEDS_PHPS && !in_array(basename($filephp), $WHITELISTED_PHPS)) {
                #unlink($filephp);
                echo $filephp . PHP_EOL;
            }
        }


        $FILES_DIR = glob($dir . '*/');

        foreach($FILES_DIR as $dir)
        {
            if(!is_dir($dir)) continue;
            #echo $dir . PHP_EOL;
            if(is_dir($dir) && $FLG_IDENTIFY_SUSPECTEDS_PHPS && !in_array(basename($dir), $WHITELISTED_DIRS)) {
                #unlink($filephp);
                echo $dir . PHP_EOL;
            }
        }


        if(is_file($dir . 'wp-config.php'))
        {

            $owner = posix_getpwuid(fileowner($dir . 'wp-config.php'));

            #print_r($owner);

            $SHELL = "chown -R " . $owner['name'] . ':' . " " . $dir;

            #echo $SHELL . PHP_EOL;

            system($SHELL);
        }
        #print_r($FILES_PHP);

        #echo $DOMAIN . " - " . $dir . PHP_EOL;
    }
}
