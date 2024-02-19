<?php

require_once("includes/std_includes.php");

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
    if ($country['disp_name'] == "UK") {
        $country['selected'] = "selected";
        continue;
    }
    $country['selected'] = "";
}

echo $m->render('new_show_fieldset', ["countries"=>$countries, "bands"=>$bands, "fieldset"=>$_GET['fieldset'], "next_fieldset"=>(int)$_GET['fieldset']+1]);