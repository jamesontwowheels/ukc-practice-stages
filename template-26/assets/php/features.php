<?php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

$game_number = $_SESSION['game'] ?? null;
$location_number = $_SESSION['location'] ?? null;

if (!$game_number || !$location_number) {
    http_response_code(400);
    echo json_encode(["error" => "Missing game_number or location_number"]);
    exit;
}

try {
    $sql = "SELECT features 
            FROM games 
            WHERE game_number = :game_number 
              AND location_number = :location_number";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':game_number', $game_number, PDO::PARAM_INT);
    $stmt->bindParam(':location_number', $location_number, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode(["error" => "Game not found"]);
        exit;
    }

    // The 'features' field already contains JSON text
    // Decode & re-encode to ensure it’s valid JSON output
    $features = json_decode($row['features'], true);

    echo json_encode([
        "game_number" => (int)$game_number,
        "location_number" => (int)$location_number,
        "features" => $features
    ], JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "DB Error: " . $e->getMessage()]);
    exit;
}
?>
