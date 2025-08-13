<?php

use PDO\PDOException;

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

function get_filter_name($db, $col, $tbl, $filter, $id) {
    try {
        $query = "SELECT $col FROM $tbl WHERE $filter = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result[0][$col];
    }
    catch(PDOException $e) {
        error_log("Couldn't retrieve band name: ".$e->getMessage());
    }
}

function getActiveYears($db, $sql_filter_arr, $search, $params) {
    $sql_filter = filterString($sql_filter_arr);
    if (isset($sql_filter_arr['filter_year']) && $sql_filter_arr['filter_year'] != "" &&!isset($sql_filter_arr['filter_band'])) {
        $sql_filter = 1;
        $search = "";
        $params = [];
    }
    try {
        $query = "SELECT
            DISTINCT YEAR(show_date) AS active_year
            FROM shows
            JOIN bands ON shows.band_id = bands.band_id
            JOIN countries ON shows.country_id = countries.country_id
            WHERE $sql_filter $search
            ORDER BY active_year DESC;";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDO_EXCEPTION $e) {
        echo "Error retrieving active years: ".$e->getMessage();
    }
}

function getActiveCountries($db, $sql_filter_arr, $search, $params) {
    $sql_filter = filterString($sql_filter_arr);
    if (isset($sql_filter_arr['filter_country']) && $sql_filter_arr['filter_country'] != "" &&!isset($sql_filter_arr['filter_band'])) {
        $sql_filter = 1;
        $search = "";
        $params = [];
    }
    try {
        $query = "SELECT
            DISTINCT disp_name, shows.country_id 
            FROM shows
            JOIN bands ON shows.band_id = bands.band_id
            JOIN countries ON shows.country_id = countries.country_id
    WHERE $sql_filter $search
    ;";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDO_EXCEPTION $e) {
        echo "Error retrieving active countries: ".$e->getMessage();
    }
}

function getStats($db, $sql_filter_arr, $search, $params) {
    $sql_filter = filterString($sql_filter_arr);
    try {
        $query = "SELECT
            COUNT(show_id) AS shows,
            COUNT(DISTINCT YEAR(show_date)) AS years,
            COUNT(DISTINCT shows.country_id) AS countries,
            COUNT(DISTINCT city) AS cities,
            COUNT(DISTINCT shows.band_id) AS bands
        FROM shows
        JOIN bands ON shows.band_id = bands.band_id
        JOIN countries ON shows.country_id = countries.country_id
        WHERE $sql_filter $search
        ;";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stats = $result[0];
        $stats['show_pl'] = $stats['shows'] > 1 ? "shows" : "show";
        $stats['year_pl'] = $stats['years'] > 1 ? "years" : "year";
        $stats['country_pl'] = $stats['countries'] > 1 ? "countries" : "country";
        $stats['city_pl'] = $stats['cities'] > 1 ? "cities" : "city";
        $stats['band_pl'] = $stats['bands'] > 1 ? "bands" : "band";
        return $stats;
    }
    catch (PDO_EXCEPTION $e) {
        echo "Error retrieving stats: ".$e->getMessage();
    }
}