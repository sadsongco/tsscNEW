<?php

// open database connection
include_once("includes/std_includes.php");

$artpath = "/assets/web/discography_images/";
$audiopath = "/assets/audio/discog/";
$noimage = "/assets/web/graphics/notfound.jpg";

$filtered_by = "";

if (isset($_GET['discog_filter'])) {
    $discog_filter = $_GET['discog_filter'];
    $query = "SELECT discog_id FROM discog_roles WHERE role_id = ?;";
    $stmt = $db->prepare($query);
    $stmt->execute([$discog_filter]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($result as $row) {
        $discog_ids[] = $row['discog_id'];
    }
    $query = "SELECT role FROM roles WHERE role_id = ?;";
    $stmt = $db->prepare($query);
    $stmt->execute([$discog_filter]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $filtered_by = ucwords($result['role']);
}

$cond = "";
if (isset($discog_ids)) {
    $cond = "WHERE id IN (".implode(",", $discog_ids).")";
}

$query = "SELECT id FROM discography
        $cond
        ORDER BY year DESC, id DESC;";

$roles_query = "SELECT role_id, role FROM roles;";

try {
    $stmt = $db->prepare($query);
    $stmt->execute();
    $discog_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $db->prepare($roles_query);
    $stmt->execute();
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($roles as &$role) {
        $role['role'] = ucwords($role['role']);
    }
}
catch (PDOException $ex) {
    $output[] = '"ERROR"'.$ex;
}

echo $m->render('discog', ["discog_entries"=>$discog_entries, "roles"=>$roles, "filtered_by"=>$filtered_by]);