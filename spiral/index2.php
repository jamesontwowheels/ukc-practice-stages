<?php
// Define the URL to fetch data from
$url = "https://p.fne.com.au:8886/resultsGetPublicForEvent?eventName=Weybridge%20Nov24%20PXAS%20ScoreQ60";

// Fetch data from the URL
$response = file_get_contents($url);

// Check if the response is valid
if ($response === FALSE) {
    die("Error fetching data.");
}

// Decode the JSON data into an associative array
$data = json_decode($response, true);

// Verify the JSON decoding was successful
if ($data === NULL || !isset($data['results'])) {
    die("Error decoding JSON data or invalid structure.");
}

// Function to calculate score based on ControlId
function calculateControlScore($controlId) {
    $firstDigit = (int)substr($controlId, 0, 1); // Extract the first digit of the ControlId
    return $firstDigit * 10; // Return score (10 for 10-19, 20 for 20-29, etc.)
}

// Initialize an array to store the competitors' progress over time
$raceData = [];

// Parse each competitor's data
foreach ($data['results'] as $participant) {
    // Get competitor's name
    $name = $participant['Firstname'] . " " . $participant['Surname'];

    // Extract punches
    $punches = $participant['Punches'] ?? [];

    // Sort punches by TimeAfterStartSecs to ensure we show the progression in order
    usort($punches, function($a, $b) {
        return $a['TimeAfterStartSecs'] - $b['TimeAfterStartSecs'];
    });

    // Prepare the time and cumulative score data
    $times = [];
    $scores = [];
    $cumulativeScore = 0;

    foreach ($punches as $punch) {
        $controlId = (int)$punch['ControlId'];
        $score = calculateControlScore($controlId); // Calculate score for the control
        $cumulativeScore += $score; // Add the score to the cumulative total

        $times[] = $punch['TimeAfterStartSecs'];
        $scores[] = $cumulativeScore; // Store the cumulative score at each time
    }

    // Store the race data for the competitor
    $raceData[] = [
        'name' => $name,
        'times' => $times,
        'scores' => $scores
    ];
}

// Output the results as an HTML page with a chart
?>
<!DOCTYPE html>
<html>
<head>
    <title>Race Chart - Cumulative Scores Over Time</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #ffffff, #e0f7fa);
            margin: 0;
            padding: 0;
        }
        h2 {
            text-align: center;
            color: #d32f2f;
            font-size: 2em;
            margin-top: 20px;
            text-shadow: 1px 1px 4px #c62828;
        }
        #raceChart {
            width: 90%;
            max-width: 900px;
            margin: 30px auto;
            height: 1800px
        }
        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
    <h2>🎄 Race Chart - Cumulative Scores Over Time 🎄</h2>

    <canvas id="raceChart"></canvas>

    <footer>
        🎅 Happy Holidays and Good Luck to All Competitors! 🎅
    </footer>

    <script>
        // Data for the chart
        var raceData = <?php echo json_encode($raceData); ?>;

        // Prepare datasets for the chart
        var datasets = raceData.map(function(competitor) {
            return {
                label: competitor.name,
                data: competitor.times.map(function(time, index) {
                    return {x: time, y: competitor.scores[index]};
                }),
                borderColor: '#' + Math.floor(Math.random()*16777215).toString(16), // Random color
                fill: false,
                tension: 0.1
            };
        });

        // Create the chart
        var ctx = document.getElementById('raceChart').getContext('2d');
        var raceChart = new Chart(ctx, {
            type: 'line',
            data: {
                datasets: datasets
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom',
                        title: {
                            display: true,
                            text: 'Time (seconds after start)'
                        }
                    },
                    y: {
                        type: 'linear',
                        title: {
                            display: true,
                            text: 'Cumulative Score'
                        },
                        min: 0
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Race Progression - Cumulative Scores Over Time'
                    }
                }
            }
        });
    </script>
</body>
</html>
