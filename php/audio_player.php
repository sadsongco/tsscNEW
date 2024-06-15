<?php

include (__DIR__."/includes/print_r2.php");
include (__DIR__."/includes/std_includes.php");

$track = $_POST; //VALIDATE?
$track["host"] = getHost();

// p_2($_POST);
// $track["title"] = str_replace("_", " ", $track["title"]);
// $track["notes"] = str_replace("_", " ", nl2br($track["notes"]));

// echo $m->render("audioTrack", $track);