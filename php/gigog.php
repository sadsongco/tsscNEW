<?php

require_once("includes/std_g_includes.php");
include_once("includes/print_r2.php");


try {
    $params=[];
    if (isset($_GET['filter'])) {
        $filter = "WHERE ".$_GET['filter']."_id = :filter_id";
        $params = ["filter_id"=>$_GET['id']];
    }
    $query = "SELECT
        show_id,
        ROW_NUMBER() OVER (
            ORDER BY show_date ASC
        ) row_num
    FROM shows
    $filter
     ORDER BY show_date ASC;";
     $stmt = $db->prepare($query);
     $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

catch (PDO_EXCEPTION $e) {
    echo "Database error: ".$e->getMessage();
}

echo $m->render('gigography', $result);
