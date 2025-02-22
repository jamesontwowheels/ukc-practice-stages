<?php
session_start();
require "db_connect.php";

if (!isset($_SESSION['username'])) {
    echo json_encode(["success" => false, "error" => "No username in session"]);
    exit;
}

$username = $_SESSION['username'];

try {
    $query = "INSERT INTO players (username) VALUES (:username)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->execute();

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Database error"]);
}
?>
