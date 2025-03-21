<?php 
session_start();

$game = 995; // Hardcoded for now
$location = 0; // Hardcoded for now

require 'db_connect.php'; // Ensure this file sets up a PDO connection

try {
    // Check if the game already exists
    $sql = "SELECT Started FROM dbo.LiveGames WHERE Location = :location AND Game = :game";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':game', $game, PDO::PARAM_INT);
    $stmt->bindParam(':location', $location, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // Game exists, check if it has already started
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row['Started'] == 1) {
            $debug_log[] = 'Game already started';
        } else {
            // Game exists but hasn't started, so start it
            $sql = "UPDATE dbo.LiveGames SET Started = 1 WHERE Location = :location AND Game = :game";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':game', $game, PDO::PARAM_INT);
            $stmt->bindParam(':location', $location, PDO::PARAM_INT);
            $stmt->execute();

            $debug_log[] = 'Game started successfully';
        }
    } else {
        // Game does not exist, insert a new record
        $sql = "INSERT INTO dbo.LiveGames (Location, Game, Started) VALUES (:location, :game, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':game', $game, PDO::PARAM_INT);
        $stmt->bindParam(':location', $location, PDO::PARAM_INT);
        $stmt->execute();

       $debug_log[] = 'Game created and started successfully';
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
