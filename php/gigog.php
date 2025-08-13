<?php

require_once("includes/std_g_includes.php");
include_once("includes/print_r2.php");

$render_params = [];
$sql_filter = "";
$params=[];

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
    ORDER BY show_date DESC, show_id DESC;";
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
