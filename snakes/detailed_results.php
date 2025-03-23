<?PHP
session_start();
$_SESSION['user_ID'] = 29;
 $_SESSION['location'] = 0;
 $_SESSION['game'] = 995;

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Leaderboard</title>
    <link rel="stylesheet" href="assets/css/results_styles.css">
</head>
<body>

    <h1>Game Leaderboard</h1>

    <table id="gameTable">
        <thead>
            <tr>
                <th>Team</th>
                <th>Snake Score</th>
                <th>Bonus</th>
                <th>Total</th>
                <th>Snakes</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <!-- Table data will be inserted here by script.js -->
        </tbody>
    </table>

    <script src="assets/js/results.js"></script>

</body>
</html>
