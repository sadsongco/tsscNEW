<?php

// open database connection
include_once("includes/std_includes.php");

$artpath = "/assets/web/discography_images/";
$audiopath = "/assets/audio/discog/";
$noimage = "/assets/web/graphics/notfound.jpg";

$query = "SELECT    id FROM discography
        ORDER BY year DESC, id DESC;";

try {
    $stmt = $db->prepare($query);
    $stmt->execute();
    $output = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $ex) {
    $output[] = '"ERROR"'.$ex;
}

echo $m->render('discog', $output);