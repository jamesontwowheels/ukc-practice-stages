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
<link rel="manifest" href="/manifest.json">

</head>
<body>
<a class="stage" href="scrabble_stages.php"><img src="images/scrabble-cover.jpeg"><div class="stage-title">Scrabble +</div></a><br>
<a class="stage" href="deep_blue_stages.php"><img src="images/deep-blue-cover.jpeg"><div class="stage-title">Deep Blue<div></a><br>

<form action="logout.php" method="post">
    <button type="submit">Logout</button>
</form>
</body>