<?php

// open database connection
require_once("../../../secure/scripts/db_pdo_connect.php");
include_once("../includes/print_r2.php");

try {
    $query = "SELECT 
                discography.id,
                (SELECT prod_clips.clip_id FROM prod_clips) AS audio
        FROM discography
        ORDER BY year DESC, discography.id DESC;
    ";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
    // {
    //     if (!$row['imgurl']) $row['imgurl'] = $noimage;
    //     else $row['imgurl'] = $artpath.$row['imgurl'];
    //     $clip_arr = [];
        $query2 = "SELECT clip_id AS id, title, artist, url, notes
        FROM prod_clips
        WHERE discography_id = ?;";
    //     $stmt2 = $db->prepare($query2);
    //     $stmt2->execute(array($row['id']));
    //     while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC))
    //     {
    //         $row2['url'] = $audiopath.$row2['url'];
    //         $clip_arr[] = $row2;
    //     }
    //     $row['audio'] = $clip_arr;
    //     $output[] = $row;
    // }
}
catch (PDOException $ex) {
    $result[] = '"ERROR"'.$ex;
}

p_2($result);