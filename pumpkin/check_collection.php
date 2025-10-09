<?php
require '../db_connect.php';
$user_id = $_GET['user_id'] ?? '';
$pumpkin_id = $_GET['pumpkin_id'] ?? '';

$stmt = $conn->prepare("SELECT COUNT(*) FROM collections WHERE user_id = :uid AND pumpkin_id = :pid");
$stmt->execute([':uid' => $user_id, ':pid' => $pumpkin_id]);
$collected = $stmt->fetchColumn() > 0;
echo json_encode(['collected' => $collected]);
?>
