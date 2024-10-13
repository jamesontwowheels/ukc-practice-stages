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
<h2>MINDGAMES</h2>
<a class="stage" href="deep_blue?location=0">Morden</a><br>

<form action="logout.php" method="post">
    <button type="submit">Logout</button>
</form>
</body>