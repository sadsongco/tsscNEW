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

if (isset($_GET['filter_country']) && $_GET['filter_country'] != "") {
    $sql_filter .= "\tAND shows.country_id = :filter_country";
    $params["filter_country"] = $_GET['filter_country'];
    $render_params["filter_country"] = $_GET['filter_country'];
    $render_params["filter_country_name"] = get_filter_name($db, "disp_name", "countries", "country_id", $_GET['filter_country']);
}

if (isset($_GET['filter_band']) && $_GET['filter_band'] != "") {
    $sql_filter .= "\n\tAND shows.band_id = :filter_band";
    $params["filter_band"] = $_GET['filter_band'];
    $render_params["filter_band"] = $_GET['filter_band'];
    $render_params["filter_band_name"] = get_filter_name($db, "band_name", "bands", "band_id", $_GET['filter_band']);
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
    OR countries.disp_name LIKE :search)";
    $params["search"] = $search_term;
    $render_params["search"] = $_GET['search'];
}

try {
    $query = "SELECT
        show_id,
        ROW_NUMBER() OVER (
            ORDER BY show_date ASC
        ) row_num
    FROM shows
    JOIN bands ON shows.band_id = bands.band_id
    JOIN countries ON shows.country_id = countries.country_id
    WHERE 1
    $sql_filter
    $search
     ORDER BY show_date ASC;";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $render_params['shows'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

catch (PDO_EXCEPTION $e) {
    echo "Database error: ".$e->getMessage();
}

echo $m->render('gigography', $render_params);
