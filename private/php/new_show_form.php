<?php

require_once("includes/std_includes.php");

try {
    $query = "SELECT country_id, disp_name FROM countries ORDER BY disp_name;";
    $countries = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

catch (PDO_EXCEPTION $e) {
    echo "Error retrieving countries from databse: ".$e->getMessage();
}
try {
    $query = "SELECT band_id, band_name FROM bands ORDER BY band_name;";
    $bands = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

catch (PDO_EXCEPTION $e) {
    echo "Error retrieving bands from databse: ".$e->getMessage();
}

echo $m->render('new_show_form', ["fieldset"=>1, "next_fieldset"=>2, "countries"=>$countries, "bands"=>$bands]);