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
$team_name = trim($input['name'] ?? '');

$player_id = $_SESSION['user_ID'];
$game = $_SESSION['game'];
$location = $_SESSION['location'];

if (strlen($team_name) < 2) {
    http_response_code(400);
    echo json_encode(['error' => 'Team name too short']);
    exit;
}

// Check existence
$stmt = $conn->prepare(
    "SELECT COUNT(*) FROM teams WHERE name = :name AND game = :game AND location = :location"
);
$stmt->execute([':name' => $team_name, ':game' => $game, ':location' => $location]);
if ($stmt->fetchColumn() > 0) {
    http_response_code(409);
    echo json_encode(['error' => 'Team already exists']);
    exit;
}

// Create team
$stmt = $conn->prepare(
    "INSERT INTO teams (name, game, location) VALUES (:name, :game, :location)"
);
$stmt->execute([':name' => $team_name, ':game' => $game, ':location' => $location]);
$newId = $conn->lastInsertId();

// Auto-join creator
$stmt = $conn->prepare(
    "DELETE FROM team_members WHERE player_ID = :player_id AND game = :game AND location = :location"
);
$stmt->execute([':player_id' => $player_id, ':game' => $game, ':location' => $location]);
$stmt = $conn->prepare(
    "INSERT INTO team_members (team, player_ID, game, location) VALUES (:team, :player_id, :game, :location)"
);
$stmt->execute([':team' => $newId, ':player_id' => $player_id, ':game' => $game, ':location' => $location]);

// Return new team
echo json_encode(['id' => (int)$newId, 'name' => $team_name]);
