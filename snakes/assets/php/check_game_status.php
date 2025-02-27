<?php
session_start();
require 'db_connect.php'; // Ensure this file sets up a PDO connection

$current_game = $_SESSION['current_game']; // Assuming game ID is stored in session

//NEW DATABASE TABLE REQUIRED FOR GAMES AND THEIR START STATUS AND START TYPE AND START TIME



try {
    $sql = "SELECT started FROM games WHERE game_id = :game_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':game_id', $current_game, PDO::PARAM_STR);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $started = $row ? (bool) $row['started'] : false;

    echo json_encode(['started' => $started]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}


