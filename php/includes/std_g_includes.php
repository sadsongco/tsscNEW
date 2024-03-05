<?php

// open database connection
require_once(__DIR__."/../../../secure/scripts/tssc_g_connect.php");

// helpers
include_once(__DIR__."/print_r2.php");

// templateing
require __DIR__.'/../../lib/mustache.php-main/src/Mustache/Autoloader.php';
Mustache_Autoloader::register();

$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader(__DIR__.'/../templates'),
    'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__.'/../templates/partials')
));

function getHost() {
    $protocol = 'http';
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') $protocol .= 's';
    return "$protocol://".$_SERVER['HTTP_HOST'];
}

function getBands($db) {
    try {
        $query = "SELECT band_id, band_name FROM bands ORDER BY band_name;";
        return $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    catch (PDO_EXCEPTION $e) {
        echo "Error retrieving bands from database: ".$e->getMessage();
    }
}