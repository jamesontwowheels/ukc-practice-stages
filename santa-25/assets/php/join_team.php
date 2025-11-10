<?php
session_start();
header('Content-Type: application/json');

include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$team_id = intval($input['team_id'] ?? 0);

$player_id = $_SESSION['user_ID'];
$game = $_SESSION['game'];
$location = $_SESSION['location'];

// Remove existing membership
$stmt = $conn->prepare(
    "DELETE FROM team_members WHERE player_ID = :player_id AND game = :game AND location = :location"
);
$stmt->execute([':player_id' => $player_id, ':game' => $game, ':location' => $location]);

// Add to new team
$stmt = $conn->prepare(
    "INSERT INTO team_members (team, player_ID, game, location) VALUES (:team, :player_id, :game, :location)"
);
$ok = $stmt->execute([
    ':team' => $team_id,
    ':player_id' => $player_id,
    ':game' => $game,
    ':location' => $location
]);

if ($ok) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to join team']);
}