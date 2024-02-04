<?php

// open database connection
require_once(__DIR__."/../../../secure/scripts/db_pdo_connect.php");

// helpers
include_once(__DIR__."/print_r2.php");

// templateing
require __DIR__.'/../../lib/mustache.php-main/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader(__DIR__.'/../templates'),
    'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__.'/../templates/partials')
));

