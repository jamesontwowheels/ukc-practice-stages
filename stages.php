<?php
// dashboard.php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit;
}?>
<head>

<link rel="stylesheet" href="geo/assets/css/main.css">
</head>
<body>
<a class="stage" href="geo">Scrabble</a><br>

<form action="logout.php" method="post">
    <button type="submit">Logout</button>
</form>
</body>