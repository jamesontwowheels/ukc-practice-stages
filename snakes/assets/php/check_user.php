<?php
session_start();
require "db_connect.php"; // This file should handle the DB connection

// Assume user is stored in a session variable
if (!isset($_SESSION['user_ID'])) {
    echo json_encode(["registered" => false]);
    exit;
}

$user_ID = $_SESSION['user_ID'];

try {
    $query = "SELECT COUNT(*) AS count FROM team_members WHERE player_ID = :user_ID";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":user_ID", $user_ID);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(["registered" => $result["count"] > 0]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error"]);
}
?>
