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
</head>
<body>
  <h1>MINDGAMES</h1>
<div id="main">

  <div id="leaderboard" class="bucket">
  <h3>Leaderboard</h3>
  <p>be patient...</p>
  <div id="leaderboard_zone">
  <table id="leaderBoard_table" border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows will be added here -->
        </tbody>
    </table>
  </div>  
  </div>
<h3><a href="index.php"><< back to the game</a></h3>
</div>

<div class="app-buttons">
        <a href="index.php" class="app-button" id="app1">App 1</a>
        <a href="leaderboard.php" class="app-button" id="app2">App 2</a>
        <a href="../stages.php" class="app-button" id="app3">App 3</a>
</div>
</body>
<script type="text/javascript" src='assets/js/leaderboard.js' defer></script>
<script type="text/javascript" src='assets/js/app-buttons.js'></script>
