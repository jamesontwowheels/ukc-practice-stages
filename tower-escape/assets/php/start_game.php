<?php 
session_start();

$location = intval($_SESSION['location']);//$_SESSION('location');
$game = intval($_SESSION['game']);//$_SESSION('location');

require 'db_connect.php'; // Ensure this file sets up a PDO connection

try {
    // Check if the row with matching location and game exists (trim and case-insensitive)
    $sql = "SELECT 1 FROM dbo.LiveGames WHERE LTRIM(RTRIM(Location)) = :location AND LTRIM(RTRIM(Game)) = :game";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':game', $game, PDO::PARAM_INT);
    $stmt->bindParam(':location', $location, PDO::PARAM_INT);
    $stmt->execute();
    $debug_log['sql'] = "$sql and $game and $location";
    if ($stmt->rowCount() > 0) {
        // Row exists, do nothing
      //  echo json_encode(['status' => 'Row already exists, no action needed']);
    } else {
        // Row does not exist, insert a new row
        $sql = "INSERT INTO dbo.LiveGames (Location, Game, Started) VALUES (:location, :game, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':game', $game, PDO::PARAM_INT);
        $stmt->bindParam(':location', $location, PDO::PARAM_INT);
        $stmt->execute();

      //  echo json_encode(['status' => 'Row created successfully']);
    }
} catch (PDOException $e) {
  //  echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
