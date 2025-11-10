<?php
session_start();
header('Content-Type: application/json');

if (
    !isset($_SESSION['user_ID'], $_SESSION['game'], $_SESSION['location'])
) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing session data']);
    exit;
}

require 'db_connect.php';

$playerId = (int) $_SESSION['user_ID'];
$game     = (int) $_SESSION['game'];
$location = $_SESSION['location']; // keep as string if your DB stores it that way

try {
    $stmt = $conn->prepare("
        SELECT t.UID AS id, t.name
        FROM team_members m
        JOIN teams t ON t.UID = m.team
        WHERE m.player_ID = :player
          AND m.game = :game
          AND m.location = :location
    ");
    $stmt->execute([
        ':player'   => $playerId,
        ':game'     => $game,
        ':location' => $location
    ]);

    $team = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($team ?: []);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'details' => $e->getMessage()
    ]);
}
