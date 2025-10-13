<?php
session_start();
require_once 'db_connect.php';
header('Content-Type: text/html; charset=utf-8');

// Ensure session user exists
if (!isset($_SESSION['user_ID'])) {
    echo "<p>No user session found. Please refresh or start a new session.</p>";
    exit;
}

$userID = $_SESSION['user_ID'];

try {
    // 1️⃣ Look up user in prize_draw table
    $sql = "SELECT Entered, name, classname FROM prize_draw WHERE player_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$userID]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo "<p>User not found in prize draw table.</p>";
    } else {

    // 2️⃣ If not entered, show the entry form
    if ((int)$row['Entered'] === 0) {
        echo <<<HTML
            <form id="prize-draw-form" method="POST" action="enter_prizedraw.php" class="prize-draw-form">
                <label for="name">Name:</label><br>
                <input type="text" id="name" name="name" required><br><br>

                <label for="classname">Classname:</label><br>
                <input type="text" id="classname" name="classname" required><br><br>

                <button type="submit">Enter Draw</button>
            </form>
        HTML;
    } 
    // 3️⃣ If entered, show a thank-you message
    else {
        $safeName = htmlspecialchars($row['name']);
        echo "<p>🎉 Thanks, <strong>{$safeName}</strong> — you’ve already entered the prize draw!</p>";
    }
    }
} catch (PDOException $e) {
    echo "<p>Error checking prize draw status: " . htmlspecialchars($e->getMessage()) . "</p>";
}
