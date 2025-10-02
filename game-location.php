<?php
// dashboard.php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit;
}

?>

<head>

<link rel="stylesheet" href="main.css">
<link rel="stylesheet" href="assets/css/app-buttons.css">
<link rel="stylesheet" href="assets/css/game-details.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link rel="manifest" href="/manifest.json">
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>

<h2><?= htmlspecialchars($_SESSION['game_name']); ?></h2>
<br>
<?php

require 'db_connect.php'; // provides $conn (PDO instance)

try {
    $sql = "SELECT Id, location_name, location_number, game_rules 
            FROM games 
            WHERE game_number = :game_number";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':game_number' => $_SESSION['game']]);
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($games) {
        foreach ($games as $game) {
            echo '<a href="'.$_SESSION['game_src'].'/lobby.php?location=' . htmlspecialchars($game['location_number']) . '">';
            echo '<button class="game_rules">' . htmlspecialchars($game['location_name']) . '</button>';
            echo '</a><br>';
        }
    } else {
        die("❌ No game found");
    }

} catch (PDOException $e) {
    die("❌ Database Error: " . $e->getMessage());
}
?>

<div id="footer-back"></div>
<div id="footer">
<div class="app-buttons">
        <a href="index.php" class="app-button" id="app1"><i class="fas fa-house"></i><br></a>
        <a href="profile.php" class="app-button" id="app2"><i class="fas fa-address-card"></i><br></a>
        <a href="faq.php" class="app-button" id="app3"><i class="fas fa-circle-question"></i><br></a>
</div>
</div>
</body>