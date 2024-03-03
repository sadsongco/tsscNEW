<?php

require_once("includes/std_includes.php");

function nextDay($date) {
    return date('Y-m-d', strtotime($date . " +1 day"));
}

// set parameters


$selected_date = isset($_GET['date']) ? nextDay($_GET['date']) : date('Y-m-d');

$fieldset = isset($_GET['fieldset']) ? (int)$_GET['fieldset'] : 0;
$next_fieldset = $fieldset + 1;
$remove_fieldset = $fieldset > 0 ? $fieldset : false;

try {
    $query = "SELECT country_id, disp_name FROM countries ORDER BY disp_name;";
    $countries = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    $query = "SELECT band_id, band_name FROM bands ORDER BY band_name;";
    $bands = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

catch (PDO_EXCEPTION $e) {
    echo "Database error getting countries: ".$e->message();
}

$countries = selectCountry($countries);
$bands = selectBand($bands);
$selected_band = getSelectedBand();
$selected_country = getSelectedCountry();

echo $m->render('new_show_fieldset', [
    "countries"=>$countries,
    "bands"=>$bands,
    "fieldset"=>(int)$_GET['fieldset'],
    "next_fieldset"=>(int)$_GET['fieldset']+1,
    "remove_fieldset"=>$remove_fieldset,
    "date_value"=>$selected_date,
    "selected_band"=>$selected_band,
    "selected_country"=>$selected_country,
    "selected_date"=>$selected_date
]);

