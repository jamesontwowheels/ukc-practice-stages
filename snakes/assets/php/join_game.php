<?php
session_start();
require "db_connect.php";

if (!isset($_SESSION['user_ID'])) {
    echo json_encode(["success" => false, "error" => "No username in session"]);
    exit;
}

$user_ID = $_SESSION['user_ID'];
$location = $_SESSION['location'];
$game = $_SESSION['game'];

try {
    $query = "INSERT INTO team_members (team, player_ID, game, location) VALUES (0, :player_ID, :game, :location)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":player_ID", $user_ID);
    $stmt->bindParam(":game", $game);
    $stmt->bindParam(":location", $location);
    $stmt->execute();

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Database error"]);
}
?>
