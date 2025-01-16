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
<a class="stage" href="tribe?location=0">FCS</a>
<form action="logout.php" method="post">
    <button type="submit">Logout</button>
</form>
</body>