<?php
// db_connect.php should include your database connection setup
include 'assets/php/db_connect.php';

// Start session for player identification
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: ../index.php");
    exit;
}

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
                //remove from any existing teams
                $query = "DELETE from team_members  where player_ID = :player_id AND location = :location AND game = :game";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':player_id', $player_id);
                $stmt->bindParam(':location', $location);
                $stmt->bindParam(':game', $game);
                $stmt->execute();
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
        $query = "DELETE from team_members  where player_ID = :player_id AND location = :location AND game = :game";
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

$query = "SELECT * FROM team_members where player_ID = $player_id";
$this_team_members = $conn->query($query);
foreach($this_team_members as $ttm){
    $current_team = $ttm["team"];
    foreach($teams as $team){
        if($team["UID"]==$current_team){
            $current_team_name = $team["name"];
        }
    }
}
echo "current team = $current_team_name";
?>

<head>
    <link rel="stylesheet" href="../main.css">  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management</title>
</head>
<body>
    <h2>Team Management</h2>
    <?php if (!empty($current_team)): ?>
        <p style="color: green;"><?= htmlspecialchars($current_team) ?></p>
    <?php endif; ?>
    <div class="login-container">
    <div class="login-form smallem">
    <?php if (!empty($message)): ?>
        <p style="color: green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <a href="index.php"><button>Return to Game</button></a>
    <br>
    <h3>Create a New Team</h3>
    <form method="post">
        <label for="team_name">Team Name:</label>
        <input type="text" id="team_name" name="team_name" required>
        <button type="submit" name="create_team">Create Team</button>
    </form>

    <h3>Join an Existing Team</h3>
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
    </div>
    </div>
</body>
