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
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</head>
<body>
  <h1>MINDGAMES</h1>
<div id="main">
  <div id="teams">
  <a href="teams.php" class="app-button" id="app1"><i class="fa-solid fa-people-group"></i><br>Team Set-up</a>
  </div>
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
<div id="timer_old"></div>
<div id="o2_timer"></div>
<div id="accuracy"></div>
<div id="score" class="bucket">
  <h3 id="score_zone_old"></h3>
</div>

<div id="inventory" class="bucket">
  <h3>Inventory</h3>
  <div id="inventory_zone">
    <table><tr>
      <td><ul id="output_resources"></ul></td>
      <td><ul id="output_gifts"></ul></td>
    </tr></table>
  </div>  
  </div>
<table id="checkpoints">
  <tbody>
   
    </tbody>
  </table>
<div id="base-padding" class="bucket">
</div>
</div>
</div>
</div>
<div id="footer">
  <div id="footer_info">
    <table><tr>
      <td><div id="score_zone"></div></td>
      <td><div id="timer"></div></td>
  </tr></table>
  
  </div>
<div class="app-buttons">
        <a href="index.php" class="app-button" id="app1"><i class="fas fa-person-running"></i><br>Game</a>
        <a href="leaderboard.php" class="app-button" id="app2"><i class="fas fa-list-ol"></i><br>Scores</a>
        <a href="history.php" class="app-button" id="app3"><i class="fas fa-clock-rotate-left"></i><br>History</a>
        <a href="../stages.php" class="app-button" id="app4"><i class="fas fa-door-open"></i><br>Exit</a>
</div>
</div>
</body>
<script type="text/javascript" src='assets/js/test.js' defer></script>
<script type="text/javascript" src='assets/js/rows.js'></script>
<script type="text/javascript" src='assets/js/app-buttons.js'></script>