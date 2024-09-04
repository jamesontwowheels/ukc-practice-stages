<?php
// login.php

// Hardcoded example credentials (in real applications, fetch from a database)
$valid_username = 'dan';
$valid_password = '1234';
$valid_username2 = 'anna';
$valid_password2 = 'abcd';
$valid_username3 = 'ed';
$valid_password3 = 'banana';

// Get the POST data
$username = $_POST['username'];
$password = $_POST['password'];

// Simple validation
if ($username === $valid_username && $password === $valid_password || $username === $valid_username2 && $password === $valid_password2 || $username === $valid_username3 && $password === $valid_password3) {
    // Start a session
    session_start();
    $_SESSION['username'] = $username;
    if($username == 'dan') {
        $user_ID = 1;
    } elseif ($username == 'anna'){
        $user_ID = 2;
    } else {
        $user_ID = 3;
    }
    $_SESSION['user_ID'] = $user_ID;
    
    // Send a JSON response
    echo json_encode(['success' => true]);
} else {
    // Send a failure response
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
}
