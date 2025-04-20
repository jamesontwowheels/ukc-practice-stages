<?PHP 

session_start();

if (isset($_GET['location'])) {
    // Set session variables
    $_SESSION['location'] = $_GET['location'];
} elseif (isset(($_SESSION['location']))){} else {
  // Redirect to login page if not logged in
  header("Location: ../stages.php");
  exit;
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Permission & Accuracy Checker</title>
    <link rel="stylesheet" href="assets/css/main.css?v0.1">
    <link rel="stylesheet" href="assets/css/lobby-styles.css?v0.1">
    <link rel="stylesheet" href="../assets/css/app-buttons.css?v0.1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body><script>
    var user_ID = <?php echo json_encode($_SESSION['user_ID']); ?>;
    var team_name = <?php echo json_encode($_SESSION['team_name']); ?>;
</script>
    <script src="assets/js/global.js"></script>
    <div id="go-to-start"></div>
    <div class="card"><h3>Location & Accuracy</h3>
    <p id="status">Checking permission...</p>
    <p id="accuracy"></p>
    <button id="requestBtn" style="display: none;">Request Location Access</button>
    </div>
    <script src="assets/js/location-check.js"></script>

    <div class="card"><h3>Game Registration</h3>
    <p id="reg-status">Checking registration...</p>
    <a href="teams.php" class="button" id="joinBtn" style="display: none;">Join a team</a>
    </div>
    <script src="assets/js/reg-check.js"></script>

    <div id="game-container" class="card">
        <h3>Game Status</h3>
        <div id="status-message">Waiting for the game to start...</div>
        
        <div class="countdown-container">
            <svg class="countdown-circle" width="100" height="100">
                <circle cx="50" cy="50" r="45" stroke="#3498db" stroke-width="8" fill="none" />
                <circle id="countdown-progress" cx="50" cy="50" r="45" stroke="#2ecc71" stroke-width="8" fill="none" stroke-dasharray="283" stroke-dashoffset="0" />
            </svg>
            <div id="countdown-text">10</div>
        </div>
    </div>


    <script src="assets/js/game-start-check.js"></script>
    <script src="assets/js/ready.js"></script>
</body>
<div id="footer-back"></div>
<div id="footer">
<div class="app-buttons">
        <a href="../index.php" class="app-button" id="app1"><i class="fas fa-house"></i><br></a>
        <a href="../profile.php" class="app-button" id="app2"><i class="fas fa-address-card"></i><br></a>
        <a href="../faq.php" class="app-button" id="app3"><i class="fas fa-circle-question"></i><br></a>
</div>
</div>
</html>
