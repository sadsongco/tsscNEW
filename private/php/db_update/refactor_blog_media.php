<?php

require_once(__DIR__."/../includes/discog_includes.php");

$query = "SELECT * FROM blog_title";
$result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
$errors = [];

foreach ($result AS $blog_title) {
    renumberMedia($blog_title['blog_id'], $db);
}

echo "<h2>Errors:</h2>";
p_2($errors);



function renumberMedia($blog_id, $db) {
    $query = "SELECT blog_content_id, blog_content FROM `blog_content` WHERE blog_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$blog_id]);
    $blog_content = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($blog_content AS $single_blog) {
        $options = ["l", "L", "r", "R", "c", "C", "n"];
        // echo "<pre>".htmlspecialchars($blog_content[0]['blog_content'])."</pre>";
        // update images
        $single_blog['blog_content'] = findMediaTags($single_blog['blog_content'], "b", $options, "images", "image", $blog_id, $db);
        $single_blog['blog_content'] = findMediaTags($single_blog['blog_content'], "i", $options, "images", "image", $blog_id, $db);
        // update audio
        $single_blog['blog_content'] = findMediaTags($single_blog['blog_content'], "a", $options, "audio", "audio", $blog_id, $db);
        // update footnotes
        $single_blog['blog_content'] = findMediaTags($single_blog['blog_content'], "f", $options, "footnotes", "footnote", $blog_id, $db);
        // update spotify and itunes
        $single_blog['blog_content'] = findMediaTags($single_blog['blog_content'], "s", $options, "spotify", "spotify", $blog_id, $db);
        $single_blog['blog_content'] = findMediaTags($single_blog['blog_content'], "S", $options, "spotify", "spotify", $blog_id, $db);
        $single_blog['blog_content'] = findMediaTags($single_blog['blog_content'], "m", $options, "itunes", "itunes", $blog_id, $db);
        // update video tags
        $single_blog['blog_content'] = findMediaTags($single_blog['blog_content'], "v", $options, "video", "video", $blog_id, $db);

        // echo "<pre>".htmlspecialchars($single_blog['blog_content'])."</pre>";
        $query = "UPDATE `blog_content` SET blog_content = ? WHERE blog_content_id = ?";
        try {
            $stmt = $db->prepare($query);
            $stmt->execute([$single_blog['blog_content'], $single_blog['blog_content_id']]);
        }
        catch (PDOException $e) {
            echo "ERROR updating blog content: ".$e->getMessage()."<br><br>";
        }
    }
}

function findMediaTags($single_blog, $tag, $options, $table_name, $field_name, $blog_id, $db) {
    $regex = '/(<!--{'.$tag.'::)([0-9]+)(::)?('.implode("|", $options).')?(}-->)/';
    preg_match_all($regex, $single_blog, $ids);
    if (sizeof($ids[2]) == 0) return $single_blog;
    if ($table_name == "video") return preg_replace(['/<!--{v::/', '/(::)?('.implode("|", $options).')}-->/'], "", $single_blog);
    return renumberTags($table_name, $field_name, $ids, $single_blog, $blog_id, $db);
    
}

function renumberTags($table_name, $field_name, $ids, $lines, $blog_id, $db) {
    global $errors;
    $searches = [];
    $replacements = [];
    foreach ($ids[2] AS $key=>$id) {
        try {
            $query = "SELECT blog_".$field_name."_id FROM blog_".$table_name." WHERE blog_id = ? AND blog_".$field_name."_no = ?;";
            $stmt = $db->prepare($query);
            $stmt->execute([$blog_id, round($id)-1]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (sizeof($result) == 0) {
                array_push($errors, "Blog id $blog_id - $table_name id ".($id)." - no database entry");
                return $lines;
            }
            array_push($searches, "/".$ids[0][$key]."/");
            array_push($replacements, $ids[1][$key].$result[0]['blog_'.$field_name.'_id'].$ids[3][$key].$ids[4][$key].$ids[5][$key]);
        }
        catch (PDOException $e) {
            exit("Error getting blog_".$table_name."_id: ".$e->getMessage());
        }
        
    }
    krsort($searches);
    krsort($replacements);
    $lines = preg_replace($searches, $replacements, $lines);
    return $lines;
}

// function renumberAudio($audio_ids, $lines, $blog_id, $db) {
//     global $errors;
//     $searches = [];
//     $replacements = [];
//     foreach ($audio_ids[2] AS $key=>$audio_id) {
//         try {
//             $query = "SELECT blog_audio_id FROM blog_audio WHERE blog_id = ? AND blog_audio_no = ?;";
//             $stmt = $db->prepare($query);
//             $stmt->execute([$blog_id, round($audio_id) - 1]);
//             $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
//             if (sizeof($result) == 0) {
//                 array_push($errors, "Blog id $blog_id - audio id ".($audio_id)." - no database entry");
//                 return $lines;
//             }
//             array_push($searches, "/".$audio_ids[0][$key]."/");
//             array_push($replacements, $audio_ids[1][$key].$result[0]['blog_audio_id'].$audio_ids[3][$key].$audio_ids[4][$key].$audio_ids[5][$key]);
//         }
//         catch (PDOException $e) {
//             exit("Error getting blog_audio_id: ".$e->getMessage());
//         }

//     }
//     $lines = preg_replace($searches, $replacements, $lines);
//     return $lines;
// }

// function renumberImages($image_ids, $lines, $blog_id, $db) {
//     global $errors;
//     $searches = [];
//     $replacements = [];
//     foreach($image_ids[2] AS $key=>$image_id) {
//         try {
//             $query = "SELECT blog_image_id FROM blog_images WHERE blog_id = ? AND blog_image_no = ?;";
//             $stmt = $db->prepare($query);
//             $stmt->execute([$blog_id, round($image_id) - 1]);
//             $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
//             if (sizeof($result) == 0) {
//                 array_push($errors, "Blog id $blog_id - image id ".($image_id)." - no database entry");
//                 return $lines;
//             }
//             array_push($searches, "/".$image_ids[0][$key]."/");
//             array_push($replacements, $image_ids[1][$key].$result[0]['blog_image_id'].$image_ids[3][$key].$image_ids[4][$key].$image_ids[5][$key]);
//         }
//         catch (PDOException $e) {
//             exit("Error getting blog_image_id: ".$e->getMessage());
//         }
//     }
//     $lines = preg_replace($searches, $replacements, $lines);
//     return $lines;
// }

// function renumberFootnotes($ids, $lines, $blog_id, $db) {
//     global $errors;
//     $searches = [];
//     $replacements = [];
//     foreach ($ids[2] AS $key=>$id) {
//         p_2($id);
//         try {
//             $query = "SELECT blog_footnote_id FROM blog_footnotes WHERE blog_id = ? AND blog_footnote_no = ?;";
//             $stmt = $db->prepare($query);
//             $stmt->execute([$blog_id, round($id) - 1]);
//             $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
//             if (sizeof($result) == 0) {
//                 array_push($errors, "Blog id $blog_id - footnote id ".($id)." - no database entry");
//                 return $lines;
//             }
//             array_push($searches, "/".$ids[0][$key]."/");
//             array_push($replacements, $ids[1][$key].$result[0]['blog_footnote_id'].$ids[3][$key].$ids[4][$key].$ids[5][$key]);
//         }
//         catch (PDOException $e) {
//             exit("Error getting blog_audio_id: ".$e->getMessage());
//         }
        
//     }
//     exit();
//     $lines = preg_replace($searches, $replacements, $lines);
//     return $lines;
// }