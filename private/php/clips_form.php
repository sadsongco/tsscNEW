<?php

require_once("includes/discog_includes.php");

$query = "SELECT id, artist, product, year FROM discography ORDER BY year DESC;";
$result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

$result[0]["selected"] = "selected";

echo $m->render("clips_form", ["discog_options"=>$result, "artist"=>$result[0]["artist"], "product"=>$result[0]["product"]]);