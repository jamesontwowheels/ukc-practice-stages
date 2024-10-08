<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: ../index.php");
    exit;
}

// Check if 'word' and 'score' parameters are present in the GET request
if (isset($_GET['location'])) {
    // Set session variables
    $_SESSION['location'] = $_GET['location'];
} elseif (isset(($_SESSION['location']))){} else {
  // Redirect to login page if not logged in
  header("Location: ../stages.php");
  exit;
}

?>

<head>
<script type="text/javascript">
    // Assign the PHP session variable to a JavaScript variable
    var user_ID = '<?php echo $_SESSION['user_ID']; ?>';
    console.log("userID = " + user_ID); // Outputs: cybersecurity_influencer
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src='targets.js'></script>
<script type="text/javascript" src='assets/js/distance.js'></script>
<link rel="stylesheet" href="assets/css/main.css">
<link rel="stylesheet" href="assets/css/underwater.css">
<link rel="stylesheet" href="assets/css/polar.css">
<link rel="stylesheet" href="assets/css/app-buttons.css">
</head>
<body>
  <h1>MINDGAMES</h1>
<div id="main">
  <div id="water">
        <div class="bubble1"></div>
        <div class="bubble1"></div>
        <div class="bubble1"></div>
        <div class="bubble1"></div>
        <div id="mountains">
          <div class="mountain mountain-1"></div>
          <div class="mountain mountain-2"></div>
          <div class="mountain mountain-3"></div>
        </div>
<div id="timer"></div>
<div id="o2_timer"></div>

<div id="score" class="bucket">
  <h3>Running Score</h3>
  <div id="score_zone"></div>
</div>

<table id="checkpoints">
  <tbody>
   
    </tbody>
  </table>
<div id="inventory" class="bucket">
  <h3>Inventory</h3>
  <div id="inventory_zone"></div>  
  </div>
  <div id="leaderboard" class="bucket">

  <h3><a href="leaderboard.php">Leaderboard</a></h3> 
  </div>
<div id="history" class="bucket">
  <h3>History</h3>
  <button id="toggleButton">show/hide</button>
  <div id="expandableContent" class="content">
  <ul id="commentary-list">
            <!-- Array items will be appended here -->
  </ul>
  </div>
</div>
</div>
</div>
<div class="app-buttons">
        <a href="index.php" class="app-button" id="app1">Game</a>
        <a href="leaderboard.php" class="app-button" id="app2">Scores</a>
        <a href="../stages.php" class="app-button" id="app3">Exit</a>
</div>
</body>
<script type="text/javascript" src='assets/js/test.js' defer></script>
<script type="text/javascript" src='assets/js/rows.js'></script>
<script type="text/javascript" src='assets/js/app-buttons.js'></script>