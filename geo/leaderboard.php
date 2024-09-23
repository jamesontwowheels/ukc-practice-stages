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
<link rel="stylesheet" href="/geo/assets/css/main.css">
</head>
<body>
  <h1>MINDGAMES</h1>
<div id="main">

  <div id="leaderboard" class="bucket">
  <h3>Leaderboard</h3>
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

</div>
</body>
<script type="text/javascript" src='/geo/assets/js/leaderboard.js' defer></script>
