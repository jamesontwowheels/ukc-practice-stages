<?php
// db_connect.php should include your database connection setup
include 'assets/php/db_connect.php';

// Start session for player identification
session_start();
$player_id = $_SESSION['user_ID']; // Assuming player ID is stored in session
$game = 4;
$location = 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_team'])) {
        // Create a new team
        $team_name = $_POST['team_name'];
        if (!empty($team_name)) {
            $query = "INSERT INTO teams (name, game, location) VALUES (:name, :game, :location)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':name', $team_name);
            $stmt->bindParam(':game', $game);
            $stmt->bindParam(':location', $location);
            if ($stmt->execute()) {
                $team_id = $conn->lastInsertId();
                // Add player to the newly created team
                $query = "INSERT INTO team_members (team, player_ID, location, game) VALUES (:team_id, :player_id, :location, :game)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':team_id', $team_id);
                $stmt->bindParam(':player_id', $player_id);
                $stmt->bindParam(':location', $location);
                $stmt->bindParam(':game', $game);
                $stmt->execute();
                $message = "Team '$team_name' created successfully!";
            } else {
                $message = "Failed to create team.";
            }
        } else {
            $message = "Team name cannot be empty.";
        }
    } elseif (isset($_POST['join_team'])) {
        // Join an existing team
        $team_id = $_POST['team_id'];
        $query = "DELETE * from team_members  where player_ID = :player_id AND location = :location AND game = :game";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':player_id', $player_id);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':game', $game);
        $stmt->execute();

        $query = "INSERT INTO team_members (team, player_ID,location, game) VALUES (:team_id, :player_id, :location, :game)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':team_id', $team_id);
        $stmt->bindParam(':player_id', $player_id);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':game', $game);
        if ($stmt->execute()) {
            $message = "Successfully joined the team.";
        } else {
            $message = "Failed to join the team.";
        }
    }
}

// Fetch existing teams
$query = "SELECT * FROM teams";
$teams = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management</title>
</head>
<body>
    <h1>Team Management</h1>

    <?php if (!empty($message)): ?>
        <p style="color: green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <h2>Create a New Team</h2>
    <form method="post">
        <label for="team_name">Team Name:</label>
        <input type="text" id="team_name" name="team_name" required>
        <button type="submit" name="create_team">Create Team</button>
    </form>

    <h2>Join an Existing Team</h2>
    <form method="post">
        <label for="team_id">Select a Team:</label>
        <select id="team_id" name="team_id" required>
            <option value="">-- Select a Team --</option>
            <?php foreach ($teams as $team): ?>
                <option value="<?= $team['UID'] ?>"><?= htmlspecialchars($team['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="join_team">Join Team</button>
    </form>
</body>
</html>
