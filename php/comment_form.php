<?php

// open database connection
include_once("includes/std_includes.php");

echo $m->render("blogCommentForm", $_GET);