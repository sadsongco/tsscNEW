<?php

require_once("includes/std_g_includes.php");
include_once("includes/print_r2.php");

$query = "SELECT shows.*, bands.band_name
FROM shows
JOIN bands ON shows.band_id = bands.band_id
WHERE show_date = ?;";
$stmt = $db->prepare($query);
$stmt->execute([$_GET['date']]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<div id='displayShow' class='displayShow visible' hx-swap-oob='true'>";
echo "    <div class='displayClose' id='displayClose' onclick='closeDisplay()'>x</div>";
$show_date = new DateTime($_GET['date']);
echo "<h1>" . $show_date->format('l jS F Y') . "</h1>";
foreach ($result as $show) {
    echo  "<p>" . $show['band_name'] . " @ " . $show['venue'] . "</p>";
}

echo  "</div>";
