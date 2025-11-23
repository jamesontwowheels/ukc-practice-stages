<?php
// dashboard.php

session_start();

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    // Redirect to stages.php if already logged in
    header("Location: stages.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="main.css">
<link rel="manifest" href="/manifest.json">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<script>
    // Auto-login on page load if credentials exist
    window.addEventListener('DOMContentLoaded', function() {
        var savedEmail = localStorage.getItem('username');
        var savedPassword = localStorage.getItem('password');

        if (savedEmail && savedPassword) {
            autoLogin(savedEmail, savedPassword);
        }
    });

    function autoLogin(email, password) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "login.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        
        xhr.onload = function() {
            if (xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    window.location.href = "stages.php";
                } else {
                    console.warn("Auto-login failed:", response.message);
                    // Optionally, clear storage on failure:
                    // localStorage.removeItem('username');
                    // localStorage.removeItem('password');
                }
            } else {
                console.error("Error during auto-login:", xhr.status);
            }
        };

        xhr.send("email=" + encodeURIComponent(email) + "&password=" + encodeURIComponent(password));
    }

    function login(event) {
        event.preventDefault();

        var email = document.getElementById("email").value;
        var password = document.getElementById("password").value;

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "login.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        
        xhr.onload = function() {
            if (xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    localStorage.setItem('username', email);
                    localStorage.setItem('password', password);
                    window.location.href = "stages.php";
                } else {
                    alert("Login failed: " + response.message);
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
