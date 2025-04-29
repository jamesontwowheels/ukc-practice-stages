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
<link rel="stylesheet" href="main.css">
<link rel="manifest" href="/manifest.json">
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
                        //alert("Login successful!");
                        window.location.href = "stages.php"; // Redirect to a protected page
                    } else {
                        alert("Login failed: " + response.message + " email: " + response.email + " query: " +response.query);
                    }
                }
            };
            
            xhr.send("email=" + encodeURIComponent(email) + "&password=" + encodeURIComponent(password));
        }
    </script>
</head>
<body>
    
  <h2>MINDGAMES</h2>
  <div id="main">
    <div class="login-container">
    <form class="login-form" onsubmit="login(event)">
    <h3>Login</h3>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        
        <button type="submit">Login</button>
        
        <a href="register.html">Register</a><br><br>
        <a href="forgotten_password.php">Forgot password</a><br>
    </form>
    </div>
    </div>
</body>
</html>
