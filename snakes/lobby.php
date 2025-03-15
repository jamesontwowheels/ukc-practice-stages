<h2?PHP 

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
    <link rel="stylesheet" href="assets/css/lobby-styles.css">
</head>
<body>

    <div class="card"><h2>Location & Accuracy</h2>
    <p id="status">Checking permission...</p>
    <p id="accuracy"></p>
    <button id="requestBtn" style="display: none;">Request Location Access</button>
    </div>
    <script src="assets/js/location-check.js"></script>

    <div class="card"><h2>Game Registration</h2>
    <p id="reg-status">Checking registration...</p>
    <a href="teams.php" class="button" id="joinBtn" style="display: none;">Join Game</a>
    </div>
    <script src="assets/js/reg-check.js"></script>

    <div id="game-container" class="card">
        <h2>Game Status</h2>
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

</body>
</html>
