<?php
session_start();
require_once 'db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_ID'])) {
    echo json_encode(['status' => 'error', 'message' => 'No session found']);
    exit;
}

$userID = $_SESSION['user_ID'];
$name = trim($_POST['name'] ?? '');
$classname = trim($_POST['classname'] ?? '');

if ($name === '' || $classname === '') {
    echo json_encode(['status' => 'error', 'message' => 'Missing name or classname']);
    exit;
}

try {
    $sql = "UPDATE prize_draw 
            SET name = ?, classname = ?, Entered = 1 
            WHERE player_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $classname, $userID]);

    echo json_encode(['status' => 'ok', 'message' => 'Entry recorded']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
