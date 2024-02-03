<?php

// include utility functions
include_once("includes/print_r2.php");

// initialise variables
$output = [];
$content = [];

    $content['title'] = "Who are we kidding? No shows likely for the time being, but sign up for the mailing list to find out soonest whenever there are!";
    $content['content'] = "<script charset=\"utf-8\" src=\"https://widget.bandsintown.com/main.min.js\"></script><a class=\"bit-widget-initializer\" data-artist-name=\"The Sad Song Co.\" data-display-local-dates=\"false\" data-display-past-dates=\"true\" data-auto-style=\"false\" data-text-color=\"#bbb\" data-link-color=\"#cca\" data-popup-background-color=\"#1c1c1a\" data-background-color=\"#1c1c1a7f\" data-display-limit=\"15\" data-link-text-color=\"#555\"></a>";

    $output[] = $content;
echo json_encode($output);

?>