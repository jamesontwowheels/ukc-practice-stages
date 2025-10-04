<?php
require_once '../db_connect.php';

// Fetch game names and numbers from the reference table
$games = [];

$stmt = $conn->prepare("SELECT game_name, game_number FROM game_reference_data ORDER BY game_name");
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $games[] = $row;
}

// Close statement (optional in PDO)
$stmt = null;

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Game</title>
  <script>
    // Populate game number when a game is selected
    function updateGameNumber() {
      const gameSelect = document.getElementById('game_name');
      const selectedOption = gameSelect.options[gameSelect.selectedIndex];
      const gameNumber = selectedOption.getAttribute('data-number');
      document.getElementById('game_number').value = gameNumber || '';
    }
  </script>
</head>
<body>
  <h1>Add New Game</h1>
  <form action="add_game.php" method="post" enctype="multipart/form-data">
    
    <label for="game_name">Game Name:</label>
    <select id="game_name" name="game_name" onchange="updateGameNumber()" required>
      <option value="">-- Select a Game --</option>
      <?php foreach ($games as $game): ?>
        <option value="<?= htmlspecialchars($game['game_name']) ?>"
                data-number="<?= htmlspecialchars($game['game_number']) ?>">
          <?= htmlspecialchars($game['game_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <br><br>

    <label for="game_number">Game Number:</label>
    <input type="number" id="game_number" name="game_number" readonly required><br><br>

    <label for="location_name">Location Name:</label>
    <input type="text" id="location_name" name="location_name" required><br><br>

    <label for="game_date">Game Date:</label>
    <input type="date" id="game_date" name="game_date"><br><br>

    <label for="game_rules">Game Rules:</label>
    <input type="number" id="game_rules" name="game_rules" value="1" required><br><br>

    <label for="kml_file">Upload KML File:</label>
    <input type="file" id="kml_file" name="kml_file" accept=".kml" required><br><br>

    <button type="submit" name="action" value="dryrun">Dry-Run Validation</button>
    <button type="submit" name="action" value="add">Add Game</button>
  </form>

  <p><a href="view_games.php">View All Games</a></p>
</body>
</html>
