<?php
session_start();
header('Content-Type: application/json');

include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$game = $_SESSION['game'];
$location = $_SESSION['location'];

$stmt = $conn->prepare(
    "SELECT UID AS id, name FROM teams WHERE game = :game AND location = :location ORDER BY name"
);
$stmt->bindParam(':game', $game);
$stmt->bindParam(':location', $location);
$stmt->execute();
$teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($teams);