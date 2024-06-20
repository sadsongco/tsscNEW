<?php

require_once(__DIR__."/../includes/discog_includes.php");

$sql = file_get_contents("blog_content.sql");

try {
    $db->query($sql);
}
catch (Exception $e) {
    echo $e->getMessage();
    exit();
}

echo "Blogs inserted";