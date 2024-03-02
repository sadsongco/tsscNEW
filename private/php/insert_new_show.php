<?php

require_once("includes/std_includes.php");

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
    }
    catch (PDO_EXCEPTION $e) {
        exit ("error inserting new show: ".$e->getMessage());
    }

    echo $m->render('new_show_form', ["fieldset"=>1, "next_fieldset"=>2, "status"=>"Shows inserted into database"]);
}