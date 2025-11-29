<?php
session_start();
require_once 'db_connect.php';

// Ensure all session variables exist
if (!isset($_SESSION['location'], $_SESSION['game'], $_SESSION['user_ID'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Missing session']);
    exit;
}

$game = $_SESSION['game'];
$location = $_SESSION['location'];
$userID = $_SESSION['user_ID'];

// Fetch distinct CP_IDs, excluding 998 and 999
$stmt = $conn->prepare("
    SELECT DISTINCT CP_ID
    FROM test_game
    WHERE Player_ID = :userID
      AND game = :game
      AND location = :location
      AND CP_ID NOT IN (998, 999)
");
$stmt->execute([
    ':userID' => $userID,
    ':game' => $game,
    ':location' => $location
]);

$cpIDs = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Ensure 3x3 grid (positions 1-9)
$grid = [];
for ($pos = 1; $pos <= 9; $pos++) {
    $grid[$pos] = in_array($pos, $cpIDs) ? "assets/img/w{$pos}.png" : "assets/img/w11.png";
}

// Return JSON
header('Content-Type: application/json');
echo json_encode(array_values($grid));
