<?php
session_start();
require "db_connect.php"; // This file should handle the DB connection

// Check if the user is logged in
if (!isset($_SESSION['user_ID'])) {
    echo json_encode(["registered" => false]);
    exit;
}

$user_ID = $_SESSION['user_ID'];
$location = $_SESSION['location'];
$game = $_SESSION['game'];

try {
    // Updated column and table names based on your schema
    $query = "
        SELECT tm.team AS team_ID, t.name as team_name
        FROM dbo.team_members tm
        JOIN dbo.teams t ON tm.team = t.UID
        WHERE tm.player_ID = :user_ID AND tm.location = :location AND tm.game = :game
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":user_ID", $user_ID);
    $stmt->bindParam(":location", $location);
    $stmt->bindParam(":game", $game);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode([
            "registered" => true,
            "team_ID" => $result["team_ID"],
            "team_name" => $result["team_name"]
        ]);
    } else {
        echo json_encode(["registered" => false]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error"]);
}
