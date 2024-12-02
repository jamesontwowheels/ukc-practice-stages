<?php
header('Content-Type: application/json');
    include 'db_connect.php';

    $stmt = $conn->query("SELECT COUNT(*) AS count FROM thingdoer");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['count' => $row['count']]);

?>
