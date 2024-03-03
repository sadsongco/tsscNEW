<?php

require_once("includes/std_includes.php");

$shows_inserted = 0;

foreach ($_POST['show'] AS $show) {
    try {
        $query = "INSERT INTO shows VALUES
        (NULL, 
        :date,
        :event,
        :venue,
        :town,
        :city,
        NULL,
        :country,
        :notes,
        NULL,
        NULL,
        NULL,
        :band);";

        $stmt = $db->prepare($query);
        $stmt->execute($show);
        $shows_inserted++;
    }
    catch (PDO_EXCEPTION $e) {
        exit ("error inserting new show: ".$e->getMessage());
    }
}

$plural = $shows_inserted > 1 ? "shows" : "show";
$countries = getCountries($db);
$bands = getBands($db);

echo $m->render('new_show_form', ["fieldset"=>1, "next_fieldset"=>2, "status"=>"$shows_inserted $plural inserted into database", "countries"=>$countries, "bands"=>$bands]);