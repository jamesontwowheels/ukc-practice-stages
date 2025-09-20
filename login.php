<?php
session_start();
include 'db_connect.php'; // login.php

// Get the POST data
try {
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'].'';
        $password = $_POST['password'];

        // Prepare a SQL statement to select the user based on the email
       // $stmt = $conn->prepare("SELECT id, name, email, password FROM dbo.users WHERE id = 8");
        // $stmt->bindParam(':email', $email);
       $stmt = $conn->prepare("SELECT * FROM dbo.users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
        
            
            // Verify the password
            if (strtolower($password) == strtolower($result['password'])) {
                // Password is correct
                $_SESSION['user_ID'] = $result['id'];
                $_SESSION['username'] = $result['name'];
               
                echo json_encode(['success' => true]);
                // Redirect to a protected page or dashboard
                // header("Location: dashboard.php");
                exit();
            } else {
                // Password is incorrect
                echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
            }
        } else {
            // No user found with the provided email
            echo json_encode(['success' => false, 'message' => 'No account found with that email address.', 'email' => $query]);
        }
    }
} 
catch (PDOException $e) {
    // Handle any errors that occur during the connection or query
    echo json_encode (['success' => false, 'message' => "Error: " . $e->getMessage(), 'query' => $query]);
}
