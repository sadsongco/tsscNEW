<?php

require_once("includes/std_includes.php");

echo $m->render("discog_form", ["year"=>date("Y")]);