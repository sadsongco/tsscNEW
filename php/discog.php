<?php

// open database connection
// require_once("../../secure/scripts/db_pdo_connect.php");
include_once("includes/std_includes.php");

$artpath = "/assets/web/discography_images/";
$audiopath = "/assets/audio/";
$noimage = "/assets/web/graphics/notfound.jpg";

$query = "SELECT    id,
                    artist,
                    product AS title,
                    year,
                    role,
                    notes,
                    cover_art AS imgurl,
                    itunes_link,
                    spotify_link
        FROM discography
        ORDER BY year DESC, id DESC;";

try {
    $stmt = $db->prepare($query);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
    {
        if (!$row['imgurl']) $row['imgurl'] = $noimage;
        else $row['imgurl'] = $artpath.$row['imgurl'];
        $clip_arr = [];
        $query2 = "SELECT clip_id AS id, title, artist, url, notes
        FROM prod_clips
        WHERE discography_id = ?;";
        $stmt2 = $db->prepare($query2);
        $stmt2->execute(array($row['id']));
        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC))
        {
            $row2['url'] = $audiopath.$row2['url'];
            $clip_arr[] = $row2;
        }
        $row['audio'] = $clip_arr;
        $output[] = $row;
    }
}
catch (PDOException $ex) {
    $output[] = '"ERROR"'.$ex;
}            

echo $m->render('discog', $output);