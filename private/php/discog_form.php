<?php

require_once("includes/discog_includes.php");

try {
    $query = "SELECT * FROM roles ORDER BY role;";
    $roles = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    echo "Error retrieving roles from database: ".$e->getMessage();
}


echo $m->render("discog_form", ["year"=>date("Y"), "roles"=>$roles]);