<?php
// login.php

// Hardcoded example credentials (in real applications, fetch from a database)
$valid_username = 'dan';
$valid_password = '1234';
$valid_username2 = 'anna';
$valid_password2 = 'abcd';

// Get the POST data
$username = $_POST['username'];
$password = $_POST['password'];

// Simple validation
if ($username === $valid_username && $password === $valid_password || $username === $valid_username2 && $password === $valid_password2) {
    // Start a session
    session_start();
    $_SESSION['username'] = $username;
    if($username == 'dan') {
        $user_ID = 1;
    } else {
        $user_ID = 2;
    }
    $_SESSION['user_ID'] = $user_ID;
    
    // Send a JSON response
    echo json_encode(['success' => true]);
} else {
    // Send a failure response
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
}