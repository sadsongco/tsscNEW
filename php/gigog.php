<?php

require_once("includes/std_g_includes.php");
include_once("includes/print_r2.php");

$render_params = [];
$sql_filter = "";
$params=[];

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

$sql_filter_arr = [];
if (isset($_GET['filter_country']) && $_GET['filter_country'] != "") {
    $sql_filter_arr['filter_country'] = "shows.country_id = :filter_country";
    $params["filter_country"] = $_GET['filter_country'];
    $render_params["filter_country"] = $_GET['filter_country'];
    $render_params["filter_country_name"] = get_filter_name($db, "disp_name", "countries", "country_id", $_GET['filter_country']);
}

if (isset($_GET['filter_band']) && $_GET['filter_band'] != "") {
    $sql_filter_arr['filter_band'] = "shows.band_id = :filter_band";
    $params["filter_band"] = $_GET['filter_band'];
    $render_params["filter_band"] = $_GET['filter_band'];
    $render_params["filter_band_name"] = get_filter_name($db, "band_name", "bands", "band_id", $_GET['filter_band']);
}

if (isset($_GET['filter_year']) && $_GET['filter_year'] != "") {
    $sql_filter_arr['filter_year'] = "YEAR(show_date)= :filter_year";
    $params["filter_year"] = $_GET['filter_year'];
    $render_params["filter_year"] = $_GET['filter_year'];
    $render_params["filter_year_name"] = $_GET['filter_year'];
}


$search = "";
if (isset($_GET) && isset($_GET['search'])) {
    $render_params['search'] = $_GET['search']; // sanitise this
    $search_term = "%".$_GET['search']."%";
    $search = "\tAND 
    (event LIKE :search
    OR venue LIKE :search
    OR town LIKE :search
    OR city LIKE :search
    OR bands.band_name LIKE :search
    OR countries.disp_name LIKE :search
    OR notes LIKE :search)";
    $params["search"] = $search_term;
    $render_params["search"] = $_GET['search'];
}

$sql_filter = filterString($sql_filter_arr);

try {
    $query = "SELECT
        show_id,
        ROW_NUMBER() OVER (
            ORDER BY show_date ASC
        ) row_num
    FROM shows
    JOIN bands ON shows.band_id = bands.band_id
    JOIN countries ON shows.country_id = countries.country_id
    WHERE $sql_filter $search
    ORDER BY show_date ASC;";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $render_params['shows'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

catch (PDO_EXCEPTION $e) {
    echo "Database error: ".$e->getMessage();
}

$render_params['active_years'] = getActiveYears($db, $sql_filter_arr, $search, $params);
$render_params['active_bands'] = getBands($db, $sql_filter_arr, $search, $params);
$render_params['active_countries'] = getActiveCountries($db, $sql_filter_arr, $search, $params);
$render_params['stats'] = getStats($db, $sql_filter_arr, $search, $params);

// p_2($render_params);

echo $m->render('gigography', $render_params);
