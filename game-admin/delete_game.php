<?php
require '../db_connect.php'; // provides $conn

$id = $_GET['id'] ?? null;
if (!$id) {
    die("❌ Invalid ID.");
}

try {
    $stmt = $conn->prepare("DELETE FROM games WHERE Id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: view_games.php");
    exit;
} catch (PDOException $e) {
    die("❌ Error: " . $e->getMessage());
}
