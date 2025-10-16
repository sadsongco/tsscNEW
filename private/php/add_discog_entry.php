<?php

require_once(__DIR__."/includes/discog_includes.php");

// constants
include(__DIR__."/includes/return_bytes.php");
include(__DIR__."/includes/file_upload.php");
define("IMAGE_UPLOAD_PATH", __DIR__."/../../assets/web/discography_images/");
define("AUDIO_UPLOAD_PATH", __DIR__."/../../user_area/assets/media/");
define("MAX_IMAGE_WIDTH", 200);
define("MAX_FILE_SIZE", return_bytes(ini_get("upload_max_filesize")));
define("MAX_POST_SIZE", return_bytes(ini_get("post_max_size")));

if (sizeof($_FILES) == 0) exit("no cover art selected");

// resize and upload cover art
$uploaded_files = [];
if (!isset($_FILES) || !isset($_FILES["cover_file"])) {
    $uploaded_files[] = ["success"=>false, "messsage"=>"No files uploaded"];
} else {
    $cover_file = $_FILES["cover_file"];
}

if ($cover_file["size"] > MAX_FILE_SIZE || $cover_file["size"] > MAX_POST_SIZE) {
    exit("Cover image file size too large");
}

try {
    $image = null;
    $image_fnc = "";
    $image_file_type = strtolower(pathinfo($cover_file["name"],PATHINFO_EXTENSION));
    $uploaded_file = $cover_file["tmp_name"];
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
        $target_filename = $_POST["cover_art"] == "" ? $cover_file["name"] : explode(".", $_POST["cover_art"])[0].".$image_file_type";
        if (!resizeImage($image, IMAGE_UPLOAD_PATH.$target_filename, $image_file_type, MAX_IMAGE_WIDTH)) {
            throw new Exception("Failed to resize image");
        }
        $_POST["cover_art"] = $target_filename;
    }
}
catch (Exception $e){
    echo "Error uploading cover artwork: ";
    exit ($e->getMessage());
}

// enter into database
try {
    $db->beginTransaction();
    $params = [
        "artist"=>$_POST['artist'],
        "product"=>$_POST['product'],
        "year"=>$_POST['year'],
        "notes"=>$_POST['notes'],
        "cover_art"=>$_POST['cover_art'],
        "itunes_link" => $_POST['itunes_link'],
        "spotify_link" => $_POST['spotify_link']
    ];
    $query = "INSERT INTO discography
        (artist,
        product,
        year,
        notes,
        cover_art,
        itunes_link,
        spotify_link)
    VALUES (
        :artist,
        :product,
        :year,
        :notes,
        :cover_art,
        :itunes_link,
        :spotify_link
    );";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    // insert roles
    $discog_id = $db->lastInsertId();
    $role_insert_arr = [];
    if (isset($_POST['role'])) {
        foreach ($_POST['role'] as $role) {
            $role_insert_arr[] = "(" . $discog_id . ", " . $role . ")";
        }
    }
    if (sizeof($role_insert_arr) > 0) {
        $query = "INSERT INTO discog_roles (discog_id, role_id) VALUES " . implode(", ", $role_insert_arr) . ";";
        $stmt = $db->prepare($query);
        $stmt->execute();
    }
    $db->commit();
}
catch (Exception $e) {
    $db->rollback();
    echo "Error inserting into databse: ";
    exit($e->getMessage());
}


echo "Discography entry added to database";