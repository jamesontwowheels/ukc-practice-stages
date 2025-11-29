<?php
session_start();

if (true || isset($_GET['location'])) {
    // Set session variables
    $_SESSION['location'] = 3; //$_GET['location'];
    $game = 666; // TBC!!!
$_SESSION['game'] = $game;
} elseif (isset(($_SESSION['location']))){} else {
  // Redirect to login page if not logged in
  header("Location: ../stages.php");
  exit;
}

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Game Map</title>
<script src="assets/js/id_check_create.js"></script>
<script type="text/javascript">
    // Assign the PHP session variable to a JavaScript variable
    var user_ID = '<?php echo $_SESSION['user_ID']; ?>';
    console.log("userID = " + user_ID); // Outputs: cybersecurity_influencer
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src='targets.js'></script>
<link rel="stylesheet" href="assets/css/main.css?v0.12">
<link rel="stylesheet" href="assets/css/app-buttons.css?v0.11">
<link rel="stylesheet" href="assets/css/leaflet.css?v0.11">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/map.css">
</head>

<body>

    <!-- The map fills the whole screen -->
    <div id="map"></div>

    <!-- Checkpoint Option Cards -->
    <div id="cp_options"></div>

    <!-- GPS Button -->
    <button id="gps_button" class="floating-btn">📍</button>

    <!-- Main map logic -->
    <script src="assets/js/map2.js"></script>
    <script src="assets/js/distance.js"></script>

</body>
</html>
