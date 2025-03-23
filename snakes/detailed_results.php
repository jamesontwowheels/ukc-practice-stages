<?PHP
session_start();
$_SESSION['user_ID'] = 29;
 $_SESSION['location'] = 0;
 $_SESSION['game'] = 995;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Scores</title>
    <link rel="stylesheet" href="assets/css/results_styles.css">
</head>
<body>
    <div class="container">
        <h1>Snakes & Ladders: Final Scores</h1>
        <div class="table-wrapper">
            <table id="gameTable">
                <thead>
                    <tr>
                        <th>Team</th>
                        <th>Snake Score</th>
                        <th>Bonus</th>
                        <th>End Point</th>
                        <th>Total</th>
                        <th>Snakes</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be inserted here by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="assets/js/results.js"></script>
</body>
</html>