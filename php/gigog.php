<?php

require_once("includes/std_g_includes.php");
include_once("includes/print_r2.php");

try {
    $query = "SELECT
        show_id,
        ROW_NUMBER() OVER (
            ORDER BY show_date ASC
        ) row_num
    FROM shows
     ORDER BY show_date ASC;";
    $result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

catch (PDO_EXCEPTION $e) {
    echo "Database error: ".$e->getMessage();
}

echo $m->render('gigography', $result);
