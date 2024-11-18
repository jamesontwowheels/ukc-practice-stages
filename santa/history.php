<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: ../index.php");
    exit;
}

// Check if 'word' and 'score' parameters are present in the GET request
if (isset($_SESSION['location'])) {
    // Set session variables
   
} else {
  // Redirect to login page if not logged in
  header("Location: ../stages.php");
  exit;
}

?>

<head>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="assets/css/main.css">
<link rel="stylesheet" href="assets/css/app-buttons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</head>
<body>
  <h1>MINDGAMES</h1>
<div id="main">
<div id="score" class="bucket">
  <h3>Stockpile</h3>
  <div id="score_zone"></div>
</div>
<div id="history" class="bucket">
  <h3>History</h3>
  <button id="toggleButton">show/hide</button>
  <ul id="commentary-list">
            <!-- Array items will be appended here -->
  </ul>
</div>
<div id="base-padding" class="bucket">
</div>
</div>

<div class="app-buttons">
        <a href="index.php" class="app-button" id="app1"><i class="fas fa-person-running"></i><br>Game</a>
        <a href="leaderboard.php" class="app-button" id="app2"><i class="fas fa-list-ol"></i><br>Scores</a>
        <a href="history.php" class="app-button" id="app2"><i class="fas fa-clock-rotate-left"></i><br>History</a>
        <a href="../stages.php" class="app-button" id="app3"><i class="fas fa-door-open"></i><br>Exit</a>
</div>
</body>
<script type="text/javascript" src='assets/js/history.js' defer></script>
<script type="text/javascript" src='assets/js/app-buttons.js'></script>
