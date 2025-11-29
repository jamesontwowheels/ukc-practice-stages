<?php
session_start();
header('Content-Type: application/json');

require_once 'db_connect.php'; // gives you $conn (PDO connection)

if (!isset($_SESSION['user_ID'])) {
    echo json_encode(['status' => 'error', 'message' => 'No session user_ID found']);
    exit;
}

$userID = $_SESSION['user_ID'];

try {
    // 1️⃣ Check if this user_ID already exists in prize_draw
    $checkSql = "SELECT COUNT(*) AS cnt FROM prize_draw WHERE player_ID = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->execute([$userID]);
    $exists = $stmt->fetchColumn();

    if ($exists > 0) {
        echo json_encode(['status' => 'ok', 'message' => 'Player already in prize_draw']);
    } else {
        // 2️⃣ Insert new record with default placeholders
        $insertSql = "INSERT INTO prize_draw (player_ID, Entered, name, classname)
                      VALUES (?, 0, 'TBC', 'TBC')";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->execute([$userID]);

        echo json_encode(['status' => 'ok', 'message' => 'Player added to prize_draw']);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
