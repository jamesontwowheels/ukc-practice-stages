<?php
// dashboard.php

session_start();

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: stages.php");
    exit;
}?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script>
        function login(event) {
            event.preventDefault(); // Prevent the form from submitting the default way

            var email = document.getElementById("email").value;
            var password = document.getElementById("password").value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "login.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            
            xhr.onload = function() {
                if (xhr.status == 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert("Login successful!");
                        window.location.href = "stages.php"; // Redirect to a protected page
                    } else {
                        alert("Login failed: " + response.message + " email: " + response.email);
                    }
                }
            };
            
            xhr.send("email=" + encodeURIComponent(email) + "&password=" + encodeURIComponent(password));
        }
    </script>
</head>
<body>
    <h2>Login</h2>
    <form onsubmit="login(event)">
        <label for="email">email:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        
        <button type="submit">Login</button>
    </form>
</body>
</html>
