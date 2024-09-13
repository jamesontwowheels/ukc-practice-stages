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
<a class="stage" href="geo?location=0">Morden</a><br>
<a class="stage" href="geo?location=1">Nonsuch</a><br>
<a class="stage" href="geo?location=2">Hither Green</a><br>

<form action="logout.php" method="post">
    <button type="submit">Logout</button>
</form>
</body>