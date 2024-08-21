<?php
// dashboard.php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit;
}?>

<a href="pacman">Pacman</a><br>
<a href="pacman-v2">Pacman-v2</a><br>
<a href="pacman-mcr">Pacman-mcr</a><br>
<a href="geo">Geo</a><br>

<form action="logout.php" method="post">
    <button type="submit">Logout</button>
</form>