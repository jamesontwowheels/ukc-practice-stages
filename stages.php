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
<a class="stage-card" href="scrabble_stages.php"><img src="images/scrabble-cover.jpeg"><div class="stage-title">Scrabble +</div></a><br>
<a class="stage-card" href="deep_blue_stages.php"><img src="images/deep-blue-cover.jpeg"><div class="stage-title">Deep Blue</div></a><br>
<a class="stage-card" href="gps_stages.php"><img src="images/gps-test-cover.jpeg"><div class="stage-title">GPS Test</div></a><br>
<a class="stage-card" href="santa_stages.php"><img src="images/gps-test-cover.jpeg"><div class="stage-title">Santa</div></a><br>

<form action="logout.php" method="post">
    <button type="submit">Logout</button>
</form>
</body>