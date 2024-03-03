<?php

require_once("includes/std_includes.php");

// set parameters
$selected_country = isset($_GET['country']) ? $_GET['country'] : 39;
$selected_band = isset($_GET['band']) ? $_GET['band'] : 4;
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

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

foreach ($countries AS &$country) {
    if ($country['country_id'] == $selected_country) {
        $country['selected'] = "selected";
        continue;
    }
    $country['selected'] = "";
}
foreach ($bands AS &$band) {
    if ($band['band_id'] == $selected_band) {
        $band['selected'] = "selected";
        continue;
    }
    $country['selected'] = "";
}

echo $m->render('new_show_fieldset', [
    "countries"=>$countries,
    "bands"=>$bands,
    "fieldset"=>(int)$_GET['fieldset'],
    "next_fieldset"=>(int)$_GET['fieldset']+1,
    "remove_fieldset"=>$remove_fieldset,
    "date_value"=>$selected_date
]);

