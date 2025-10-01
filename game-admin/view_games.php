<?php
require '../db_connect.php'; // provides $conn

try {
    $sql = "SELECT Id, game_number, location_number, location_name, game_date, game_rules 
            FROM games
            ORDER BY game_date DESC, game_number ASC";
    $stmt = $conn->query($sql);
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("❌ Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Games</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Games List</h2>
    <p>
        <a href="add_game.html">➕ Add New Game</a>
    </p>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Game #</th>
                <th>Location #</th>
                <th>Location Name</th>
                <th>Date</th>
                <th>Rules</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($games) > 0): ?>
            <?php foreach ($games as $game): ?>
                <tr>
                    <td><?= htmlspecialchars($game['Id']) ?></td>
                    <td><?= htmlspecialchars($game['game_number']) ?></td>
                    <td><?= htmlspecialchars($game['location_number']) ?></td>
                    <td><?= htmlspecialchars($game['location_name']) ?></td>
                    <td><?= $game['game_date'] ? htmlspecialchars($game['game_date']) : '-' ?></td>
                    <td><?= htmlspecialchars($game['game_rules']) ?></td>
                    <td>
                        <a href="edit_game.php?id=<?= urlencode($game['Id']) ?>">✏️ Edit</a> | 
                        <a href="delete_game.php?id=<?= urlencode($game['Id']) ?>" onclick="return confirm('Delete this game?');">🗑 Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No games found</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
