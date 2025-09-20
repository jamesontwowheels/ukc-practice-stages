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
<link rel="stylesheet" href="assets/css/app-buttons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link rel="manifest" href="/manifest.json">
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
<h2>MINDGAMES</h2>
<a class="stage" href="deep_blue?location=8">Newcastle</a><br>    <!-- Map ID: 68ca90ab06213  -->
<a class="stage" href="deep_blue?location=7">Banstead</a><br>    <!-- Map ID: 6727beb8dd300  -->
<a class="stage" href="deep_blue?location=6">Whitley v2</a><br>  <!-- Map ID: 67cca1e7bbb10 -->
<!-- 
<a class="stage" href="deep_blue?location=5">Whitley Bay</a><br>
<a class="stage" href="deep_blue?location=3">Court Rec</a><br>
<a class="stage" href="deep_blue?location=4">Lightwater</a><br>
<a class="stage" href="deep_blue?location=2">Focus Group</a><br>
<a class="stage" href="deep_blue?location=0">Fenchurch St - 22.10.24</a><br>
<a class="stage" href="deep_blue?location=1">Rising Sun</a><br> -->

<div id="footer-back"></div>
<div id="footer">
<div class="app-buttons">
        <a href="index.php" class="app-button" id="app1"><i class="fas fa-house"></i><br></a>
        <a href="profile.php" class="app-button" id="app2"><i class="fas fa-address-card"></i><br></a>
        <a href="faq.php" class="app-button" id="app3"><i class="fas fa-circle-question"></i><br></a>
</div>
</div>
</body>