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

function filterString($sql_filter_arr) {
    return sizeof($sql_filter_arr) == 0 ? 1 : implode("\n\tAND ", $sql_filter_arr);
   }

function getBands($db, $sql_filter_arr=null, $search=null, $params=null) {
    $sql_filter = filterString($sql_filter_arr);
    if (isset($sql_filter_arr['filter_band']) && $sql_filter_arr['filter_band'] != "") {
        $sql_filter = 1;
        $search = null;
        $params = null;
    }
    try {
        $query = "SELECT DISTINCT(bands.band_id), band_name
        FROM `bands`
        JOIN `shows` ON shows.band_id = bands.band_id
        JOIN countries ON shows.country_id = countries.country_id
        WHERE $sql_filter $search
        ORDER BY band_name;";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    catch (PDO_EXCEPTION $e) {
        echo "Error retrieving bands from database: ".$e->getMessage();
    }
}