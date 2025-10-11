<?php
session_start();
header('Content-Type: application/json');

require_once 'db_connect.php';

try {
    // 1️⃣ Insert placeholder row with temporary email
    $stmt = $conn->prepare("
        INSERT INTO users (name, email, password)
        VALUES (:name, :email, :password)
    ");

    $name = 'New User';
    $password = password_hash('changeme', PASSWORD_DEFAULT);

    // Temporarily use a dummy email — we’ll fix it after insert
    $email = 'temp_' . uniqid() . '@placeholder.com';

    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':password' => $password
    ]);

    // 2️⃣ Get auto-generated ID
    $userID = $conn->lastInsertId();

    // 3️⃣ Update email to a unique format using the generated ID
    $uniqueEmail = "user_{$userID}@placeholder.com";
    $update = $conn->prepare("UPDATE users SET email = :email WHERE id = :id");
    $update->execute([':email' => $uniqueEmail, ':id' => $userID]);

    // 4️⃣ Store ID in PHP session
    $_SESSION['user_ID'] = $userID;

    // 5️⃣ Return the ID to the frontend
    echo json_encode(['status' => 'ok', 'user_ID' => $userID, 'email' => $uniqueEmail]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
