<?php

require_once("includes/std_g_includes.php");
include_once("includes/print_r2.php");

try {
    $query = "SELECT
        show_id,
        DATE_FORMAT(show_date, '%D %b %Y') AS date,
        event,
        venue,
        city,
        countries.disp_name AS country_name,
        notes,
        bands.band_name
     FROM shows
     LEFT JOIN countries ON countries.country_id = shows.country_id
     LEFT JOIN bands ON bands.band_id = shows.band_id
     WHERE show_id = ?;";
     $stmt = $db->prepare($query);
     $stmt->execute([$_GET['show_id']]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

catch (PDO_EXCEPTION $e) {
    echo "Database error: ".$e->getMessage();
}
// p_2($result[0]);
echo $m->render('gigog_entry', $result[0]);
