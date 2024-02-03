<?php

include_once("includes/print_r2.php");

// open database connection
require_once("../../secure/scripts/db_pdo_connect.php");

$output = [];
$news_query = "SELECT   news_id,
                        news_title AS title,
                        news_content AS content,
                        DATE_FORMAT(news_date, '%W %D %M, %Y') AS date
FROM news_content_copy
ORDER BY news_id DESC
LIMIT 5;";

try {
    $stmt = $db->prepare($news_query);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tmpArr = [];
        foreach ($row as $key=>$value) {
            $tmpArr[$key] = $value;
        }
        $img_arr = [];
        $image_query = "SELECT  news_image_url AS url,
                                news_image_no,
                                news_image_title AS title,
                                news_image_credit AS credit
        FROM news_images
        WHERE news_id = ".$row['news_id'].";";
        try {
            $stmt2 = $db->prepare($image_query);
            $stmt2->execute();
            while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                $img_arr[$row2['news_image_no']] = $row2;
            }
        }
        catch (PDOException $ex) {
            $img_arr[0] = '"ERROR"';
        }
        ksort($img_arr);
        $tmpArr['images'] = $img_arr;
        $output[] = $tmpArr;
    }
}
catch (PDOException $ex) {
    $output[] = '"ERROR"';
}

echo json_encode($output);

require_once("../../secure/scripts/db_pdo_disconnect.php");

?>