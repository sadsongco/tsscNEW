<?php

require_once(__DIR__."/includes/discog_includes.php");

// constants
include(__DIR__."/includes/return_bytes.php");
include(__DIR__."/includes/file_upload.php");
define("AUDIO_UPLOAD_PATH", __DIR__."/../../assets/audio/discog/");
define("MAX_IMAGE_WIDTH", 200);
define("MAX_FILE_SIZE", return_bytes(ini_get("upload_max_filesize")));
define("MAX_POST_SIZE", return_bytes(ini_get("post_max_size")));

if (sizeof($_FILES) == 0) exit ("no file selected");
if ($_FILES["audio_clip"]["type"] != "audio/mpeg") exit("wrong file type");

$target_filename = $_POST["filename"] == "" ? str_replace(" ", "_", $_FILES["audio_clip"]["name"]) : explode(".", $_POST["filename"])[0].".mp3";

// save audio
move_uploaded_file($_FILES["audio_clip"]["tmp_name"], AUDIO_UPLOAD_PATH.$target_filename);

$params = $_POST;
$params["url"] = $target_filename;
unset($params["filename"]);

// update database
try {
    $query = "INSERT INTO prod_clips VALUES (NULL, :discography_id, :artist, :title, :url, :notes);";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
}
catch (Exception $e) {
    echo "Problem updating prod clips database:";
    exit($e->getMessage());
}

echo "<h2>Prod clip added</h2>";

echo $m->render("discog_form", ["year"=>date("Y")]);