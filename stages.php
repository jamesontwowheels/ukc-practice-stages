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

<link rel="stylesheet" href="main.css?v=0.1">
<link rel="stylesheet" href="assets/css/app-buttons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link rel="manifest" href="/manifest.json">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<h2>GAME LIBRARY</h2>
<br>
<div class="stage-grid">
<!--<div class="image-container stage-card">
    <img src="images/goldrush-cover.jpeg" alt="Coming Soon">
    <div class="overlay">Coming Soon</div>
</div>
-->
<a class="stage-card" href="game-detail.php?game_number=7"><img src="images/scrabble+_cover.jpg"><div class="stage-title">Scrabble+</div></a><br>
<a class="stage-card" href="game-detail.php?game_number=9"><img src="images/santa-cover.jpeg"><div class="stage-title">Rudolph's Rounds</div></a><br>

</div>
<br><h3>Future Games</h3><br>
<div class="stage-grid">
<!--<div class="image-container stage-card">
    <img src="images/goldrush-cover.jpeg" alt="Coming Soon">
    <div class="overlay">Coming Soon</div>
</div>
-->
<a class="stage-card" href="game-detail.php?game_number=8"><img src="images/tower-escape.jpg"><div class="stage-title">Tower Escape</div></a><br>
<a class="stage-card" href="game-detail.php?game_number=7"><img src="images/scrabble+_cover.jpg"><div class="stage-title">Scrabble+</div></a><br>
<a class="stage-card" href="game-detail.php?game_number=6"><img src="images/goldrush-cover.jpeg"><div class="stage-title">Goldrush</div></a><br>
<a class="stage-card" href="game-detail.php?game_number=995"><img src="images/snakes-cover.jpg"><div class="stage-title">Snakes & Ladders</div></a><br>
<a class="stage-card" href="game-detail.php?game_number=5"><img src="images/dry-january.png"><div class="stage-title">Dry January</div></a><br>
<a class="stage-card" href="game-detail.php?game_number=4"><img src="images/santa-cover.png"><div class="stage-title">Rudolph's Rounds</div></a><br>
<a class="stage-card" href="game-detail.php?game_number=1"><img src="images/deep-blue-cover.jpeg"><div class="stage-title">Deep Blue</div></a><br>
<a class="stage-card" href="game-detail.php?game_number=777"><img src="images/gps-test-cover.jpeg"><div class="stage-title">Test Game</div></a><br>
<a class="stage-card" href="game-detail.php?game_number=9"><img src="images/gps-test-cover.jpeg"><div class="stage-title">Rudolph 2</div></a><br>
<a class="stage-card" href="gps_stages.php"><img src="images/gps-test-cover.jpeg"><div class="stage-title">GPS Test</div></a><br>
</div>
<form action="logout.php" method="post">
    <button type="submit">Logout</button>
</form>
<div id="footer-back"></div>
<div id="footer">
<div class="app-buttons">
        <a href="index.php" class="app-button" id="app1"><i class="fas fa-house"></i><br></a>
        <a href="profile.php" class="app-button" id="app2"><i class="fas fa-address-card"></i><br></a>
        <a href="faq.php" class="app-button" id="app3"><i class="fas fa-circle-question"></i><br></a>
</div>
</div>
</body>