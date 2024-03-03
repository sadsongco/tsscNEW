<?php

require_once("includes/std_includes.php");

$countries = getCountries($db);
$bands = getBands($db);

echo $m->render('new_show_form', ["fieldset"=>0, "next_fieldset"=>1, "countries"=>$countries, "bands"=>$bands]);