<?php 
session_start();
$game = $_SESSION['game'] ;
//$game = 6; // Hardcoded for now
$location = intval($_SESSION['location']); // Hardcoded for now

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
        $started = false;
        echo json_encode(['started' => $started]);
    } else {
        $started = true;
        echo json_encode(['started' => $started]);
    }

} catch (PDOException $e) {
   echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

