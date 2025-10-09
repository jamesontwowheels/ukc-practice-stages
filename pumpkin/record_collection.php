<?php
require '../db_connect.php';
$data = json_decode(file_get_contents("php://input"), true);
$stmt = $conn->prepare("
    INSERT INTO collections (user_id, pumpkin_id, latitude, longitude, collected_at)
    VALUES (:uid, :pid, :lat, :lon, GETDATE())
");
$stmt->execute([
    ':uid' => $data['user_id'],
    ':pid' => $data['pumpkin_id'],
    ':lat' => $data['lat'],
    ':lon' => $data['lon']
]);
echo json_encode(['success' => true]);
?>
