<?php

// open database connection
include_once("includes/std_includes.php");

function getCurrentBlog($db, $current_blog=false) {
    // fetch the content and title for the chosen blog
    $cond = $current_blog ? "AND blog_id = $current_blog" : "AND blog_date < NOW() ORDER BY blog_date DESC LIMIT 1";
    $query = "SELECT        blog_title.blog_id,  
                            blog_content AS content,
                            blog_title AS title,
                            blog_date,
                            DATE_FORMAT(blog_date, '%W %D %M, %Y') AS date
                FROM        blog_title
                JOIN        blog_content USING (blog_id)
                WHERE       published
                $cond
                ;";

    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result[0] as $key=>$value) {
                $output[$key] = $value;
            }
    }
    catch (PDOException $e) {
        echo "Error retrieving blogs:";
        exit($e->getMessage());
    }
    return $output;
}