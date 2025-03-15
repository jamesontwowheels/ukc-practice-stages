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
<a class="stage" href="deep_blue?location=7">Banstead</a><br>    <!-- Map ID: 6727beb8dd300  -->
<a class="stage" href="deep_blue?location=6">Whitley v2</a><br>  <!-- Map ID: 67cca1e7bbb10 -->
<a class="stage" href="deep_blue?location=5">Whitley Bay</a><br>
<a class="stage" href="deep_blue?location=3">Court Rec</a><br>
<a class="stage" href="deep_blue?location=4">Lightwater</a><br>
<a class="stage" href="deep_blue?location=2">Focus Group</a><br>
<a class="stage" href="deep_blue?location=0">Fenchurch St - 22.10.24</a><br>
<a class="stage" href="deep_blue?location=1">Rising Sun</a><br>

<form action="logout.php" method="post">
    <button type="submit">Logout</button>
</form>
</body>