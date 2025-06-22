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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Game Lobby</title>
    <link rel="stylesheet" href="assets/css/lobby.css" />
    <link rel="stylesheet" href="../assets/css/app-buttons.css?v0.1">
  <script defer src="assets/js/lobby.js"></script>
</head>
<body><script>
    var user_ID = <?php echo json_encode($_SESSION['user_ID']); ?>;
    </script>
  <div class="container">
    <h3>Game Lobby</h3>

    <!-- Progress Bar -->
    <div class="progress-bar">
      <div class="progress-step" data-step="1">
        <div class="progress-circle"></div>
        <div class="label">Location</div>
      </div>
      <div class="progress-step" data-step="2">
        <div class="progress-circle"></div>
        <div class="label">Team</div>
      </div>
      <div class="progress-step" data-step="3">
        <div class="progress-circle"></div>
        <div class="label">Wait</div>
      </div>
    </div>

    <!-- Step 1 -->
    <section id="step-location" class="step active">
      <h2>Enable Location</h2>
      <p id="location-status">Checking permission...</p>
      <button id="request-location-btn" style="display:none;">Allow Access</button>
    </section>

    <!-- Step 2 -->
    <section id="step-team" class="step">
      <h2>Pick or Create Team</h2>
      <div id="team-list" class="team-list">Loading teams...</div>
      <form id="create-team-form">
        <input type="text"
               id="new-team-name"
               placeholder="New team name"
               required minlength="2" maxlength="20" />
        <button type="submit">Create</button>
      </form>
      <p id="team-status"></p>
      <button id="confirm-team-btn" style="display:none;">Confirm Team</button>
    </section>

    <!-- Step 3 -->
    <section id="step-wait" class="step">
      <h2>Waiting for Start</h2>
      <p id="game-status">Waiting for the game to start...</p>
      <p><strong>Your Team:</strong> <span id="final-team-name">—</span></p>
      <div class="countdown-container">
        <svg class="countdown-circle" width="100" height="100">
          <circle cx="50" cy="50" r="45"
                  stroke="#444" stroke-width="6"
                  fill="none" />
          <circle id="countdown-progress"
                  cx="50" cy="50" r="45"
                  stroke-dasharray="283"
                  stroke-dashoffset="283"
                  stroke-width="6"
                  fill="none" />
        </svg>
        <div id="countdown-text">10</div>
      </div>
      <button id="change-team-btn">Change Team</button>
      <button id="go-to-start" disabled>Go to Start</button>
    </section>
  <div id="unlock-button" class ="card"></div>
  <div id="footer-back"></div>
  </div>
  
    <script src="assets/js/ready.js"></script>
  <!-- FontAwesome -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js" defer></script>
</body>
<div id="footer">
<div class="app-buttons">
        <a href="../index.php" class="app-button" id="app1"><i class="fas fa-house"></i><br></a>
        <a href="../profile.php" class="app-button" id="app2"><i class="fas fa-address-card"></i><br></a>
        <a href="../faq.php" class="app-button" id="app3"><i class="fas fa-circle-question"></i><br></a>
</div>
</div>
</html>
