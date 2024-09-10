<?php
include 'db_connect.php'; // login.php

// Get the POST data
try {
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare a SQL statement to select the user based on the email
        $stmt = $conn->prepare("SELECT id, name, email, password FROM dbo.users WHERE email = :email");
        $stmt->bindParam(':email', $email);

        // Execute the query
        $stmt->execute();

        // Check if the user exists
        if ($stmt->rowCount() > 0) {
            // Fetch the user's data
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the password
            if ($password = $user['password']) {
                // Password is correct
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];

               
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
            echo json_encode(['success' => false, 'message' => 'No account found with that email address.']);
        }
    }
} catch (PDOException $e) {
    // Handle any errors that occur during the connection or query
    echo "Error: " . $e->getMessage();
}
