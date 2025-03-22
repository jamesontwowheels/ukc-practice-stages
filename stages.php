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

<link rel="stylesheet" href="main.css">
<link rel="manifest" href="/manifest.json">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<h2>MINDGAMES</h2>
<br>
<div class="image-container stage-card">
    <img src="images/goldrush-cover.jpeg" alt="Coming Soon">
    <div class="overlay">Coming Soon</div>
</div><br>
<a class="image-container stage-card" href="snake_stages.php">
    <img src="images/snakes-cover.jpg" alt="Coming Soon">
    <div class="stage-title">Snakes & Ladders</div>
</a><br>
<a class="stage-card" href="dry_january_stages.php"><img src="images/dry-january.png"><div class="stage-title">Dry January</div></a><br>
<a class="stage-card" href="santa_stages.php"><img src="images/santa-cover.png"><div class="stage-title">Rudolph's Rounds</div></a><br>
<a class="stage-card" href="scrabble_stages.php"><img src="images/scrabble-cover.jpeg"><div class="stage-title">Scrabble +</div></a><br>
<a class="stage-card" href="deep_blue_stages.php"><img src="images/deep-blue-cover.jpeg"><div class="stage-title">Deep Blue</div></a><br>
<a class="stage-card" href="gps_stages.php"><img src="images/gps-test-cover.jpeg"><div class="stage-title">GPS Test</div></a><br>

<form action="logout.php" method="post">
    <button type="submit">Logout</button>
</form>
</body>