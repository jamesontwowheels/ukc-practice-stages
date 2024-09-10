<?php
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
        $query = "SELECT dbo.users.id, dbo.users.name, dbo.users.email, dbo.users.password FROM dbo.users WHERE dbo.users.email = concat(''.$email)";
        $result = $conn->query($query);
        // Execute the query
        //$stmt->execute();

        // Check if the user exists
        if ($result->rowCount() > 0) {
            // Fetch the user's data
            //$user = $stmt->fetch(PDO::FETCH_ASSOC);
            $user = $result->fetch_assoc();
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
            echo json_encode(['success' => false, 'message' => 'No account found with that email address.', 'email' => $query]);
        }
    }
} catch (PDOException $e) {
    // Handle any errors that occur during the connection or query
    echo json_encode (['success' => false, 'message' => "Error: " . $e->getMessage(), 'query' => $query]);
}
