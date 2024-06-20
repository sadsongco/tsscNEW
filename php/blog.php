<?php

// open database connection
include_once("includes/std_includes.php");
include_once("includes/get_current_blog_date.php");

// constants
define("RELATIVE_ROOT", "/..");
define("IMAGE_UPLOAD_PATH", "/assets/web/blog_images/");
define("AUDIO_UPLOAD_PATH", "/assets/audio/blog/");
define("HOST", getHost());

function parseBody($blog_id, $body, $db, $m) {
    $content = explode("\n", str_replace("\n\r", "\n", $body));
    $output = "<p>";
    for ($x = 0; $x < sizeof($content); $x++) {
        if ($content[$x] == "" || $content[$x] == "\n") continue;
        // $content[$x] = parseLinks($content[$x], $m);
        $content[$x] = getMedia($content[$x], $db, $m);
        if ($x+1 < sizeof($content) && ($content[$x+1] == "" || $content[$x+1] == "\n")) {
            $output .= trim($content[$x])."</p>\n<p>";
            continue;
        }
        $output .= trim($content[$x])."<br />\n";
    }
    $output .= "</p>";
    return $output;
}

function getMedia($line, $db, $m) {
    $options = ["l", "r", "L", "R", "c", "C", "n"];
    // get images, old and new tags
    $line = getTags($line, "b", $options, "getImage", $db, $m);
    $line = getTags($line, "i", $options, "getImage", $db, $m);
    // Get audio
    $line = getTags($line, "a", $options, "getAudio", $db, $m);
    // get footnotes
    $line = getTags($line, "f", $options, "getFootnotes", $db, $m);
    // get itunes and spotify
    $line = getTags($line, "m", $options, "getItunes", $db, $m);
    $line = getTags($line, "s", $options, "getSpotify", $db, $m);
    $line = getTags($line, "S", $options, "getSpotify", $db, $m);
    $line = getTags($line, "v", $options, null, null, $db, $m);

    $line = trim($line);
    return $line;
}

function getTags($line, $tag, $options, $func, $db, $m) {
    // get tags
    $regex = '/<!--{'.$tag.'::([0-9]+)::?('.implode("|", $options).')?}-->/';
    preg_match_all($regex, $line, $ids);
    if (sizeof($ids[1]) == 0) return $line;
    // if it's an embedded video, just remove the hiding tags
    if ($tag == "v") return preg_replace(['/<!--{v::(.*)\s?(.*)::[n|c|l]?}-->/'], '$1', $line);
    foreach ($ids[1] as $key=>$id) {
        $params = $func($id, $ids[2][$key], $db);
        $replace_el = $m->render($params['template'], $params);
        $replace_str = $ids[0][$key];
        return preg_replace("/$replace_str/", $replace_el, $line);
    }

}

function getImage($image_id, $image_float, $db) {
    $image = getMediaArr("blog_images", $image_id, "blog_image_id", $db);
    $image["url"] = HOST.IMAGE_UPLOAD_PATH.$image["image_url"];
    $image['path'] = __DIR__.RELATIVE_ROOT.IMAGE_UPLOAD_PATH.$image["image_url"];
    $image["thumbpath"] = HOST.IMAGE_UPLOAD_PATH."thumbnails/".$image["image_url"];
    $image_metadata = getimagesize($image["path"]);
    $image["size_string"] = $image_metadata[3];
    $image["aspect_ratio"] = $image_metadata[0] . "/".$image_metadata[1];
    $image["template"] = "blockImage";
    if ($image_float) {
        switch ($image_float) {
            case "l":
                $image["float"] = "floatLeft";
                $image["template"] = "inlineImage";
                break;
            case "r":
                $image["template"] = "inlineImage";
                $image["float"] = "floatRight";
                break;
            default:
                $image["float"] = "floatCentered";
        }
    }
    return $image;
}

function getAudio($audio_id, $align, $db) {
    $track = getMediaArr("blog_audio", $audio_id, "blog_audio_id", $db);
    $track['url'] = HOST.AUDIO_UPLOAD_PATH.$track['blog_audio_url'];
    $track['template'] = "audioPlayer";
    return $track;
}

function getFootnotes($id, $align, $db) {
    $footnotes = getMediaArr("blog_footnotes", $id, "blog_footnote_id", $db);
    $footnotes['template'] = "footnote";
    return $footnotes;
}

function getItunes($id, $align, $db) {
    $track = getMediaArr("blog_itunes", $id, "blog_itunes_id", $db);
    $track['template'] = "itunes";
    return $track;
}

function getSpotify($id, $align, $db) {
    $track = getMediaArr("blog_spotify", $id, "blog_spotify_id", $db);
    $track['spotify_link'] = parseSptfyURL($track['spotify_link']);
    $track['template'] = "spotify";
    return $track;
}

function parseSptfyURL($spotify_uri) {
    $arr = explode("/", $spotify_uri);
    if (sizeof($arr) == 1) return splitSptfyURI($spotify_uri);
    return splitSptfyURI("spotify:".$arr[3].":".$arr[4]);
}

function splitSptfyURI($spotify_uri) {
    $tmp = explode(":", $spotify_uri);
    return ["type"=>$tmp[1], "uri"=>$tmp[2]];
}

function getMediaArr($table, $id, $id_id, $db) {
    $query = "SELECT * FROM $table WHERE $id_id = ?";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([$id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result[0];
    }
    catch (PDOException $e) {
        exit("error getting media: ".$e->getMessage());
    }
}

function fetchBlogNav($date, $db, $prev=false) {
    $cond = $prev ? "blog_date < '$date' ORDER BY blog_date DESC" : "blog_date > '$date' ORDER BY blog_date ASC";
    $query = "  SELECT  blog_id AS id
                FROM    blog_title
                WHERE   published = 1
                AND     $cond
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
            return $row;
        }
    }
    catch (PDOException $ex) {
        return "";
    }
}

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


// initialise variables
$output = [];
if (!isset($_GET['blog_id']) || $_GET['blog_id'] == 0 || $_GET['blog_id'] == "" || !is_numeric($_GET['blog_id'])) $current_blog = false;
else $current_blog = $_GET['blog_id'];

$current_blog_arr = getCurrentBlog($db, $current_blog);
$current_blog = $current_blog_arr['blog_id'];
$current_blog_arr['content'] = parseBody($current_blog_arr['blog_id'], $current_blog_arr['content'], $db, $m);

echo $m->render("blog_main", $current_blog_arr);

// updated next and previous blog links
$date = $current_blog_arr['blog_date'];
$params = [];
// get prev blog
$params['prev_blog'] = fetchBlogNav($date, $db, true);
// get next blog
$params['next_blog'] = fetchBlogNav($date, $db);

echo $m->render("blogNav", $params);

// load comments for blog
$comments = fetchComments($current_blog, $db);

echo $m->render("blogComments", ["comments"=>$comments]);

// update list of other blogs
$other_blogs = fetchOtherBlogs($current_blog, $db);
echo $m->render("otherBlogs", ["other_blogs"=>$other_blogs]);