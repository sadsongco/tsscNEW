<?php

// include utility functions
include_once("includes/print_r2.php");

// open database connection
require_once("../../secure/scripts/db_pdo_connect.php");

// initialise variables
$output = [];

// fetch the content and title for the chosen blog
$cond = "";
if (!isset($_GET['blog_id']) || $_GET['blog_id'] == 0 || $_GET['blog_id'] == "" || !is_numeric($_GET['blog_id']))
    $cond = "AND blog_date = (SELECT MAX(blog_date) FROM blog_title WHERE published)";
else $cond = "AND blog_id = ".$_GET['blog_id'];

$query = "SELECT        blog_title.blog_id,  
                        blog_content AS content,
                        blog_title AS title,
                        blog_date,
                        DATE_FORMAT(blog_date, '%W %D %M, %Y') AS date
            FROM        blog_title
            JOIN        blog_content USING (blog_id)
            WHERE       published
            $cond
            AND         blog_date < NOW();";

try {
    $stmt = $db->prepare($query);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tmpArr = [];
        foreach ($row as $key=>$value) {
            $tmpArr[$key] = $value;
        }
        // fetch any tagged other content
        $tmpArr['images'] = fetch_images($row['blog_id'], $db);
        $tmpArr['comments'] = fetch_comments($row['blog_id'], $db);
        $tmpArr['footnotes'] = fetch_footnotes($row['blog_id'], $db);
        $tmpArr['spotify'] = fetch_spotify($row['blog_id'], $db);
        $tmpArr['itunes'] = fetch_itunes($row['blog_id'], $db);
        $tmpArr['quotes'] = fetch_quotes($row['blog_id'], $db);
        $tmpArr['audio'] = fetch_audio($row['blog_id'], $db);
        $tmpArr['next_blog'] = fetch_next_blog($row['blog_date'], $db);
        $tmpArr['prev_blog'] = fetch_prev_blog($row['blog_date'], $db);
        $output[0] = $tmpArr;
        $output[0]['other_blogs'] = fetch_other_blogs($row['blog_id'], $db);
    }
}
catch (PDOException $ex) {
    $output[] = '"ERROR"';
}
// print_r($tmpArr);
echo json_encode($output);

function fetch_images($blog_id, $db)
{
    $img_arr = [];
    $query = "SELECT  image_url AS url,
                            blog_image_title AS title,
                            blog_image_credit AS credit,
                            blog_image_no
    FROM blog_images
    WHERE blog_id = $blog_id;";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $img_arr[$row['blog_image_no']] = $row;
        }
    }
    catch (PDOException $ex) {
        $img_arr[0] = '"ERROR"';
    }
    ksort($img_arr);
    return $img_arr;
}

function fetch_comments($blog_id, $db, $comment_reply = 0) 
{
    $comment_arr = [];
    $query = "SELECT            blog_comment_auth AS name,
                                blog_comment_text AS comment,
                                blog_comment_id,
                                DATE_FORMAT(blog_comment_date, '%a %D %M %Y, %r') as date,
                                blog_reply_id,
                                blog_id
                    FROM        blog_comments
                    WHERE       blog_id = $blog_id
                    AND         blog_reply_id = $comment_reply
                    ORDER BY    blog_comment_date DESC;";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['replies'] = fetch_comments($blog_id, $db, $row['blog_comment_id']);
            $comment_arr[] = $row;
        }
    }
    catch (PDOException $ex) {
        $comment_arr[0] = '"ERROR"';
    }
    return $comment_arr;
}

function fetch_footnotes($blog_id, $db)
{
    $footnotes_arr = [];
    $query = "SELECT            blog_footnote AS footnote,
                                blog_footnote_no AS id
                FROM            blog_footnotes
                WHERE           blog_id = $blog_id
                ORDER BY        blog_footnote_no ASC;";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $footnotes_arr[$row['id']] = $row;
        }
    }
    catch (PDOException $ex) {
        $comment_arr[0] = '"ERROR"';
    }
    return $footnotes_arr;
}

function fetch_spotify($blog_id, $db)
{
    $this_arr = [];
    $query = "  SELECT          spotify_link AS url,
                                blog_spotify_no AS id
                FROM            blog_spotify
                WHERE           blog_id = $blog_id
                ORDER BY        blog_spotify_no ASC;";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this_arr[$row['id']] = $row;
        }
    }
    catch (PDOException $ex) {
        $this_arr[0] = '"ERROR"';
    }
    return $this_arr;
}

function fetch_itunes($blog_id, $db)
{
    $this_arr = [];
    $query = "  SELECT          blog_itunes_uri AS url,
                                blog_itunes_no AS id
                FROM            blog_itunes
                WHERE           blog_id = $blog_id
                ORDER BY        blog_itunes_no ASC;";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this_arr[$row['id']] = $row;
        }
    }
    catch (PDOException $ex) {
        $this_arr[0] = '"ERROR"';
    }
    return $this_arr;
}

function fetch_quotes($blog_id, $db)
{
    $this_arr = [];
    $query = "  SELECT          blog_quote AS quote,
                                blog_quote_attribute AS attribute,
                                blog_quote_no AS id
                FROM            blog_quotes
                WHERE           blog_id = $blog_id
                ORDER BY        blog_quote_no ASC;";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this_arr[$row['id']] = $row;
        }
    }
    catch (PDOException $ex) {
        $this_arr[0] = '"ERROR"';
    }
    return $this_arr;
}

function fetch_audio($blog_id, $db)
{
    $this_arr = [];
    $query = "  SELECT          blog_audio_url AS url,
                                blog_audio_title AS title,
                                blog_audio_artist AS artist,
                                blog_audio_no AS id
                FROM            blog_audio
                WHERE           blog_id = $blog_id
                ORDER BY        blog_audio_no ASC;";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this_arr[$row['id']] = $row;
        }
    }
    catch (PDOException $ex) {
        $this_arr[0] = '"ERROR"';
    }
    return $this_arr;
}

function fetch_other_blogs($id, $db) {
    $this_arr = [];
    $query = "  SELECT      blog_title.blog_id AS id,
                            blog_title AS title,
                            CONCAT(SUBSTRING_INDEX(blog_content, \" \", 50), \"...\") AS content,
                            DATE_FORMAT(blog_date, '%W %D %M, %Y') AS date
                FROM        blog_title
                JOIN        blog_content USING (blog_id)
                WHERE       blog_title.blog_id != $id
                AND         published
                AND         blog_date < NOW()
                ORDER BY    blog_date DESC;";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this_arr[] = $row;
        }
    }
    catch (PDOException $ex) {
        $this_arr[0] = '"ERROR"';
    }
    return $this_arr;
}

function fetch_next_blog($date, $db) {
    $query = "  SELECT  blog_id AS id
                FROM    blog_title
                WHERE   blog_date > '$date'
                LIMIT   1;";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            return "";
        }
        else {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // naughty html creeping in - lazy boy
            return "&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;<a id = \"otherblog_".$row['id']."\" href = \"?page=blog#blog_id=".$row['id']."\">NEXT BLOG</a>";
        }
    }
    catch (PDOException $ex) {
        return "";
    }
}

function fetch_prev_blog($date, $db) {
    $query = "  SELECT  blog_id AS id
                FROM    blog_title
                WHERE   blog_date < '$date'
                ORDER BY blog_date DESC
                LIMIT   1;";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            return "";
        }
        else {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return "<a id = \"otherblog_".$row['id']."\" href = \"?page=blog#blog_id=".$row['id']."\">PREVIOUS BLOG</a>";
        }
    }
    catch (PDOException $ex) {
        return "";
    }
}

require_once("../../secure/scripts/db_pdo_disconnect.php");

?>