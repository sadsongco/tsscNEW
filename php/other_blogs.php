<?php

// open database connection
include_once("includes/std_includes.php");
include_once("includes/get_current_blog_date.php");


function fetchOtherBlogs($id, $db) {
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
            $row['content'] = strip_tags($row['content']);
            $this_arr[] = $row;
        }
    }
    catch (PDOException $ex) {
        $this_arr[0] = '"ERROR"';
    }
    return $this_arr;
}
// p_2($_GET);
$current_blog = $_GET['blog_id'] == 'null' ? getCurrentBlog($db) : $_GET['blog_id'];
$other_blogs = fetchOtherBlogs($current_blog, $db);
echo $m->render("otherBlogs", ["other_blogs"=>$other_blogs]);