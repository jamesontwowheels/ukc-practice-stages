<?php
session_start();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['userID']) && !empty($input['userID'])) {
    $_SESSION['user_ID'] = $input['userID'];
    echo json_encode(['status' => 'ok', 'sessionUserID' => $_SESSION['user_ID']]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid userID']);
}
?>
