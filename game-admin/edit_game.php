<?php
require '../db_connect.php'; // provides $conn

// Get ID from query string
$id = $_GET['id'] ?? null;
if (!$id) {
    die("❌ Invalid ID.");
}

// Fetch existing record
try {
    $stmt = $conn->prepare("SELECT * FROM games WHERE Id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $game = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$game) {
        die("❌ Game not found.");
    }
} catch (PDOException $e) {
    die("❌ Error: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $game_number     = $_POST['game_number'] ?? null;
    $location_number = $_POST['location_number'] ?? null;
    $location_name   = $_POST['location_name'] ?? null;
    $game_date       = $_POST['game_date'] ?? null;
    $game_rules      = $_POST['game_rules'] ?? 1;

    try {
        $sql = "UPDATE games 
                SET game_number = :game_number, 
                    location_number = :location_number, 
                    location_name = :location_name, 
                    game_date = :game_date, 
                    game_rules = :game_rules
                WHERE Id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':game_number', $game_number, PDO::PARAM_INT);
        $stmt->bindParam(':location_number', $location_number, PDO::PARAM_INT);
        $stmt->bindParam(':location_name', $location_name, PDO::PARAM_STR);
        $stmt->bindParam(':game_date', $game_date , PDO::PARAM_STR);
        $stmt->bindParam(':game_rules', $game_rules, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        header("Location: view_games.php");
        exit;
    } catch (PDOException $e) {
        echo "❌ Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Game</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Edit Game</h2>
    <form method="post">
        <label for="game_number">Game Number:</label>
        <input type="number" id="game_number" name="game_number" value="<?= htmlspecialchars($game['game_number']) ?>" required><br><br>

        <label for="location_number">Location Number:</label>
        <input type="number" id="location_number" name="location_number" value="<?= htmlspecialchars($game['location_number']) ?>" required><br><br>

        <label for="location_name">Location Name:</label>
        <input type="text" id="location_name" name="location_name" maxlength="50" value="<?= htmlspecialchars($game['location_name']) ?>" required><br><br>

        <label for="game_date">Game Date:</label>
        <input type="date" id="game_date" name="game_date" value="<?= htmlspecialchars($game['game_date']) ?>"><br><br>

        <label for="game_rules">Game Rules:</label>
        <input type="number" id="game_rules" name="game_rules" value="<?= htmlspecialchars($game['game_rules']) ?>"><br><br>

        <button type="submit">💾 Save Changes</button>
    </form>
    <p><a href="view_games.php">⬅ Back to list</a></p>
</body>
</html>
