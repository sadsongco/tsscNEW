<?php

function resizeImage($image, $file_path, $image_file_type, $target_width) {
    $resized_image = imagescale($image, $target_width);
    switch ($image_file_type) {
        case "jpg":
        case "jpeg":
            return imagejpeg($resized_image, $file_path);
            break;
        case "png":
            return imagepng($resized_image, $file_path);
            break;
        case "gif":
            return imagegif($resized_image, $file_path);
            break;
        default:
            throw new Exception("unsupported image type");
            break;
    }
}

function saveThumbnail($image, $filename, $image_file_type) {
    $thumbnail = imagescale($image, IMAGE_THUMBNAIL_WIDTH);
    $file_path = IMAGE_UPLOAD_PATH."thumbnails/".$filename;
    switch ($image_file_type) {
        case "jpg":
        case "jpeg":
            return imagejpeg($thumbnail, $file_path);
            break;
        case "png":
            return imagepng($thhumbnail, $file_path);
            break;
        case "gif":
            return imagegif($thumbnail, $file_path);
            break;
        default:
            throw new Exception("unsupported image type");
            break;
    }
}

function uploadMedia($files, $key, $db, $table, $image_file_type = null) {
    // this is for uploads too large - change to throw a reasonable error
    if ($files["tmp_name"][$key] == "") die ("NO TMP_NAME:<br />..");
    $upload_path = $table == "images" ? IMAGE_UPLOAD_PATH : AUDIO_UPLOAD_PATH;
    $tag  = $table == "images" ? "i" : "a";
    $files["name"][$key] = str_replace(" ", "_", $files["name"][$key]);
    if (file_exists($upload_path.$files["name"][$key])) {
        return fileExists($files["name"][$key], $table, $tag, $db);
    }
    $uploaded_file = $files["tmp_name"][$key];
    try {
        $media_id = insertMediaDB($files, $key, $db, $table);
    }
    catch (PDOException $e) {
        return ["success"=>false, "message"=>"Database error: ".$e->getMessage()];
    }
    try {
        $image = null;
        $image_fnc = "";
        switch ($image_file_type) {
            case "jpg":
            case "jpeg":
                $image = imagecreatefromjpeg($uploaded_file);
                $image_fnc = "imagejpeg";
                break;
            case "png":
                $image = imagecreatefrompng($uploaded_file);
                $image_fnc = "imagepng";
                break;
            case "gif":
                $image = imagecreatefromgif($uploaded_file);
                $image_fnc = "imagegif";
                break;
            default:
                $image = null;
        }
        if ($image) {
            // resize images and save thumbnails
            $image_size = getimagesize($uploaded_file);
            if ($image_size[0] > MAX_IMAGE_WIDTH) {
                try {
                    if (!resizeImage($image, IMAGE_UPLOAD_PATH.$files["name"][$key], $image_file_type)) {
                        throw new Exception("Failed to resize image");
                    }
                    saveThumbnail($image, $files["name"][$key], $image_file_type);
                    unlink($uploaded_file);
                }
                catch (Exception $e) {
                    throw new Exception("Failed to save image: ".$e->getMessage());
                }
            }
        } else {
            // save audio
            move_uploaded_file($uploaded_file, $upload_path.$files["name"][$key]);
        }
    }
    catch (Exception $e) {
        return ["success"=>false, "message"=>"File copy error: ".$e->getMessage()];
    }
    return ["success"=>true, "filename"=>$files["name"][$key], "tag"=>"{{".$tag."::".$media_id."}}"];
}