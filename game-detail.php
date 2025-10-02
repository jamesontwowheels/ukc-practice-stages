<?php
// dashboard.php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit;
}

$_SESSION['game'] = $_GET['game_number'];

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

<?php

require 'db_connect.php'; // provides $conn (PDO instance)

try {
    $sql = "SELECT Id, game_name, game_description, game_rules, game_src
            FROM game_reference_data 
            WHERE game_number = :game_number";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':game_number' => $_SESSION['game']]);
    $game = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($game) {
        $rulesFile = "game-admin/game-rules/" . $game['game_rules'];
        $_SESSION['game_src'] = $game['game_src'];
        $_SESSION['game_name'] = $game['game_name'];
    } else {
        die("❌ No game found");
    }

} catch (PDOException $e) {
    die("❌ Database Error: " . $e->getMessage());
}
?>

<!-- Output HTML -->
<h2><?= htmlspecialchars($game['game_name']); ?></h2>
<p class="game_description"><?= nl2br(htmlspecialchars($game['game_description'])); ?></p>

<?php if (!empty($game['game_rules'])): ?>
    <a href="<?= htmlspecialchars($rulesFile); ?>" download>
        <button class="game_rules" type="button">Download Rules   <i class="fas fa-download"></i></button>
    </a>

    <br>
<a href="game-location.php"><button class="game_rules" type="button">Select Game   <i class="fas fa-forward"></i></button></a>
<?php endif; ?>

<div id="footer-back"></div>
<div id="footer">
<div class="app-buttons">
        <a href="index.php" class="app-button" id="app1"><i class="fas fa-house"></i><br></a>
        <a href="profile.php" class="app-button" id="app2"><i class="fas fa-address-card"></i><br></a>
        <a href="faq.php" class="app-button" id="app3"><i class="fas fa-circle-question"></i><br></a>
</div>
</div>
</body>