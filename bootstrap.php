<?php

chdir(__DIR__);

$composerAutoload = __DIR__ . '/vendor/autoload.php';

if(!is_file($composerAutoload))
{
    throw new RuntimeException("Unable to find autoload.php. Run 'composer install' to install dependencies");
}


require $composerAutoload;

return require __DIR__ . '/app/start.php';