<?php

require_once("includes/std_includes.php");

try {
    $query = "INSERT INTO bands VALUES (NULL, ?);";
    $stmt = $db->prepare($query);
    $stmt->execute([$_POST['band_name']]);
}
catch (PDO_EXCEPTION $e) {
    die ("Error inserting new band: ".$e->getMessage());
}

echo $m->render('new_band_form', ["result"=>"new band added"]);