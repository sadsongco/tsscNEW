<?php

// open database connection
require_once(__DIR__."/../../../../secure/scripts/db_pdo_aconnect.php");

// helpers
include_once(__DIR__."/../../../php/includes/print_r2.php");

// templateing
require __DIR__.'/../../../lib/mustache.php-main/src/Mustache/Autoloader.php';
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

function getCountries($db) {
    try {
        $query = "SELECT country_id, disp_name FROM countries ORDER BY disp_name;";
        return $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    catch (PDO_EXCEPTION $e) {
        echo "Error retrieving countries from database: ".$e->getMessage();
    }
}

function getSelectedCountry() {
    return isset($_GET['country']) ? $_GET['country'] : 39;
}

function selectCountry($countries) {
    $selected_country = getSelectedCountry();
    foreach ($countries AS &$country) {
        if ($country['country_id'] == $selected_country) {
            $country['selected'] = "selected";
            continue;
        }
        $country['selected'] = "";
    }
    return $countries;
}

function getSelectedBand() {
    return isset($_GET['band']) ? $_GET['band'] : 4;
}

function selectBand($bands) {
    $selected_band = getSelectedBand();
    foreach ($bands AS &$band) {
        if ($band['band_id'] == $selected_band) {
            $band['selected'] = "selected";
            continue;
        }
        $country['selected'] = "";
    }
    return $bands;
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