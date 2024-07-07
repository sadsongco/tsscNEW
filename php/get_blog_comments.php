<?php

// open database connection
include_once("includes/std_includes.php");

if (!isset($_GET['blog_id']) || !is_numeric($_GET['blog_id'])) exit();
$current_blog = $_GET['blog_id'];

function fetchComments($blog_id, $db, $comment_reply = 0) 
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
            $row['replies'] = fetchComments($blog_id, $db, $row['blog_comment_id']);
            $comment_arr[] = $row;
        }
    }
    catch (PDOException $ex) {
        $comment_arr[0] = '"ERROR"';
    }
    return $comment_arr;
}

// load comments for blog
$comments = fetchComments($current_blog, $db);

echo $m->render("blogCommentContent", ["comments"=>$comments]);
