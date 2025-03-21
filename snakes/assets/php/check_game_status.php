<?php 
session_start();

$game = 995; // Hardcoded for now
$location = 0; // Hardcoded for now

require 'db_connect.php'; // Ensure this file sets up a PDO connection

try {
    $sql = "SELECT Started FROM dbo.LiveGames WHERE Location = :location AND Game = :game";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':game', $game, PDO::PARAM_INT);
    $stmt->bindParam(':location', $location, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
       // echo json_encode(['error' => 'No matching game found']);
       // exit;
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $started = isset($row['Started']) ? intval($row['Started']) : 0;

   // echo json_encode(['started' => (bool) $started]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

