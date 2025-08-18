<?php

include_once("../includes/discog_includes.php");
include_once("../../../php/includes/print_r2.php");

$query = "SELECT id, role FROM discography ORDER BY id;";

$result = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {
    $row['role'] = str_replace(",", "/", $row['role']);
    $role_arr = explode("/", $row['role']);
    foreach($role_arr as $role) {
        preg_match('/(.*)(\(.*\))/', $role, $output_array);
        if (!empty($output_array)) $role = $output_array[1];
        insertRoleIntoDB(trim(strtolower($role)), $row['id'], $db);
    }
}

function insertRoleIntoDB($role, $discog_id, $db) {
    try {
        $query = "INSERT INTO roles (role) VALUES (?);";
        $stmt = $db->prepare($query);
        $stmt->execute([$role]);

        $role_id = $db->lastInsertId();
        $query = "INSERT INTO discog_roles (discog_id, role_id) VALUES (?, ?);";
        $stmt = $db->prepare($query);
        $stmt->execute([$discog_id, $role_id]);
        echo "Role $role for discography entry $discog_id added to database<br />";
    }
    catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "Role already exists<br />";
            $query = "SELECT role_id FROM roles WHERE role = ?;";
            $stmt = $db->prepare($query);
            $stmt->execute([$role]);
            $role_id = $stmt->fetch(PDO::FETCH_ASSOC);
            $role_id = $role_id['role_id'];
            $query = "INSERT INTO discog_roles (discog_id, role_id) VALUES (?, ?);";
            $stmt = $db->prepare($query);
            $stmt->execute([$discog_id, $role_id]);
            echo "Role $role for discography entry $discog_id added to database<br />";
        } else {
            echo "Error inserting into database: ";
            exit($e->getMessage());
        }
    }
}