<?php
session_start();

// Check if 'word' and 'score' parameters are present in the GET request
if (isset($_SESSION['location'])) {
    // Set session variables
   
} else {
  // Redirect to login page if not logged in
  header("Location: index.php");
  exit;
}

?>

<head>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="assets/css/main.css?v0.11">
<link rel="stylesheet" href="assets/css/app-buttons.css?v0.1">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</head>
<body>
  <h1>History</h1>
<div id="main">
<div id="score" class="bucket">
  <div id="score_zone"></div>
</div>
<div id="history" class="bucket">
  <ul id="commentary-list">
            <!-- Array items will be appended here -->
  </ul>
</div>
<div id="base-padding" class="bucket">
</div>
</div>

<div id="footer">
<div class="app-buttons">
        <a href="index.php" class="app-button" id="app1">Game</a>
        <a href="leaderboard.php" class="app-button" id="app2">Scores</a>
        <a href="history.php" class="app-button" id="app3">History</a>
</div>
</div>
<div id="exit"><a href="index.php" class="" id="app4"><i class="fas fa-arrow-left"></i></a></div>
</body>
<script type="text/javascript" src='assets/js/history.js?v0.1' defer></script>
<script type="text/javascript" src='assets/js/app-buttons.js'></script>
