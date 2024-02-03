<?php

// include utility functions
include_once("includes/print_r2.php");

// open database connection
require_once("../../secure/scripts/db_pdo_iconnect.php");

function convert_smart_quotes($string) 
{ 
    $search = array(chr(145), 
                    chr(146), 
                    chr(147), 
                    chr(148), 
                    chr(151)); 
 
    $replace = array("'", 
                     "'", 
                     '"', 
                     '"', 
                     '-'); 
 
    return str_replace($search, $replace, $string); 
}

if (isset($_POST['reply_id'])
    AND is_numeric($_POST['reply_id'])
    AND $_POST['reply_id'] > 0)
{
    $reply=1;
}else{
    $reply=0;
}
$query = "INSERT INTO blog_comments VALUES
    (NULL, ?, ?, ?, ?, ?, NOW());";
$values = [strip_tags($_POST['blog_id']),
                strip_tags(convert_smart_quotes($_POST['username'])),
                strip_tags(convert_smart_quotes($_POST['usercomment'])),
                $reply,
                strip_tags($_POST['reply_id'])];
try
{
    $stmt = $db->prepare($query);
    $stmt->execute($values);
}
catch (PDOException $ex)
{
    echo "error! $ex";
    error_log($ex, $log_url);
    exit;
}
//if all is successful email band member to let them know a comment has been left
$email_from = "nigel@thesadsongco.com";
$subject = "New comment posted on blog";
$message = "Someone posted a new comment on blog ".$_POST['blog_id'].". \n\r<br /><br />".strip_tags(convert_smart_quotes($_POST['username']))." said: \n\r<br /><br />\"".nl2br(strip_tags(convert_smart_quotes($_POST['usercomment'])))."\"\n\r<br /><br />See it here\n\r<br />https://thesadsongco.com/private/newTSSC/blogcomment.html?page=blog#blog_id=".strip_tags($_POST['blog_id']);
$to = "nigel@thesadsongco.com";
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= 'From: <nigel@thesadsongco.com>' . "\r\n";
mail($to, $subject, $message, $headers);

echo true;

require_once("../../secure/scripts/db_pdo_disconnect.php");

?>