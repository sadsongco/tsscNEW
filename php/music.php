<?php

// include utility functions
include_once("includes/print_r2.php");

// open database connection
require_once("../../secure/scripts/db_pdo_connect.php");

$output = [];
$query = "SELECT    album_id AS id,
                    album_title AS title,
                    soundcloud,
                    itunes,
                    bandcamp,
                    credits as content,
                    notes,
                    DATE_FORMAT(release_date, '%D %b %Y') AS date,
                    cover_img AS url
        FROM        tssc_albums
        ORDER BY    release_date DESC;";
try
{
	$stmt = $db->prepare($query);
	$stmt->execute();
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
        $query2 = "SELECT   track_title AS title,
                            lyrics,
                            track_no
                    FROM    tssc_tracks
                    WHERE   album_id = ".$row['id']."
                    ORDER BY track_no ASC;";
        $stmt2 = $db->prepare($query2);
        $stmt2->execute();
        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC))
        {
            $row2['lyrics'] = htmlentities($row2['lyrics']);
            $row2['album_id'] = $row['id'];
            $row['tracks'][] = $row2;
        }
        $output[] = $row;
    }
}
catch (PDOException $ex)
{
    error_log($ex, $log_url);
    exit;
}//end of PDO catch

echo json_encode($output);

require_once("../../secure/scripts/db_pdo_disconnect.php");

?>