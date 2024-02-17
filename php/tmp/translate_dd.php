<?php

try {
	$db = new PDO('mysql:host=localhost;dbname=thesadso_gigog;charset=utf8', 'root', 'cfodkipG52');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
catch (PDOException $e) {
	//db connection failed. Kill everything
	echo "Couldn't connect to the database";
	die();
	}

// $query = "SELECT city, venue, info, CONCAT('20', year, '-', month, '-', day) AS date FROM gigs;";
// $stmt = $db->prepare($query);
// $stmt->execute();
// $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// $query = "SELECT country_id FROM countries WHERE code = 'GB';";
// $stmt = $db->prepare($query);
// $stmt->execute();
// $country_id = $stmt->fetch(PDO::FETCH_ASSOC);

// $query = "SELECT band_id FROM bands WHERE band_name = 'Dive Dive';";
// $stmt = $db->prepare($query);
// $stmt->execute();
// $band_id = $stmt->fetch(PDO::FETCH_ASSOC);

// foreach ($result AS $dd_show) {
//     $query = "INSERT INTO shows VALUES (NULL, ?, '', ?, '', ?, NULL, ?, ?, '', NULL, NULL, ?)";
//     $stmt = $db->prepare($query);
//     $stmt->execute([$dd_show['date'], $dd_show['venue'], $dd_show['city'], $country_id['country_id'], $dd_show['info'], $band_id['band_id']]);
// }

$query = "SELECT show_date, venue, city, disp_name, band_name
    FROM shows
    JOIN bands ON shows.band_id = bands.band_id
    JOIN countries ON shows.country_id = countries.country_id
    ORDER BY show_date ASC;";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($result);
echo "</pre>";