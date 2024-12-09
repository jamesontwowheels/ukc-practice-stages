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

// Function to find the longest sequence of consecutive ControlIds increasing by one
// when ordered by TimeAfterStartSecs, skipping wrong ControlIds
function findLongestChronologicalConsecutive($punches) {
    // Sort punches by TimeAfterStartSecs
    usort($punches, function($a, $b) {
        return $a['TimeAfterStartSecs'] - $b['TimeAfterStartSecs'];
    });

    $longestSequence = [
        "sequence" => [],
        "score" => 0
    ];
    $currentSequence = [];
    $expectedControlId = 1;
    $running_score = 0;

    foreach ($punches as $punch) {
        $controlId = (int)$punch['ControlId'];

        if ($expectedControlId === null || $controlId === $expectedControlId) {
            // Start a new sequence or continue the current one
            $currentSequence[] = $controlId;
            $score = ($expectedControlId % 5 ) * 10;
            if($score == 0) { $score = 50;};
            $running_score += $score;
            $expectedControlId = $controlId + 1; // Update the expected ControlId
        } elseif ($controlId > $expectedControlId) {
            break;
            // Missed the expected ControlId; save the current sequence if it's the longest
            if (count($currentSequence) > count($longestSequence)) {
                $longestSequence["sequence"] = $currentSequence;
            }
            // Reset the sequence and attempt to start from the current control
         //   $currentSequence = [$controlId];
         //   $expectedControlId = $controlId + 1;
        }
        // Otherwise, skip this control and continue checking
    }

    // Final check after the loop
    if (count($currentSequence) > count($longestSequence)) {
        $longestSequence["sequence"] = $currentSequence;
    }
    $longestSequence["sequence"] = $currentSequence;
    $longestSequence["score"] = $running_score;
    return $longestSequence;
}

// Initialize an array to store the results for each competitor
$competitorSequences = [];

// Parse each competitor's data
foreach ($data['results'] as $participant) {
    // Get competitor's name
    $name = $participant['Firstname'] . " " . $participant['Surname'];

    // Extract punches
    $punches = $participant['Punches'] ?? [];

    // Find the longest sequence of consecutive ControlIds in chronological order
    $longestSequence = findLongestChronologicalConsecutive($punches);
    $penalty = 0; //$participant['TotalTimeSecs']; 
    $seconds = $participant['TotalTimeSecs'];
    $secs_late = max($seconds - 3600,0);
    if($secs_late > 0) {
        $penalty = ceil($secs_late/2);
    };

    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $remainingSeconds = $seconds % 60;
    $pretty_time = sprintf("%02d:%02d:%02d", $hours, $minutes, $remainingSeconds);

    $final_score = $longestSequence["score"] - $penalty;

    // Store the result
    $competitorSequences[] = [
        'name' => $name,
        'sequence' => $longestSequence["sequence"],
        'length' => count($longestSequence["sequence"]),
        'score' => $longestSequence["score"],
        'time' => $pretty_time,
        'time_secs' => $seconds,
        'penalty' => $penalty,
        'final score' => $final_score
    ];
}

// Sort the array by the length of the sequence in descending order
usort($competitorSequences, function($a, $b) {
    if ($b['final score'] == $a['final score']) {
        // Secondary comparison: id
        return $a['time_secs'] - $b['time_secs'];
    }
    return $b['final score'] - $a['final score'];
});

// Output the results as an HTML table
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rob's Streeto Spiral</title>
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
        table {
            width: 90%;
            max-width: 900px;
            margin: 20px auto;
            border-collapse: collapse;
            font-size: 1.2em;
            background-color: #fffaf0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #d32f2f;
        }
        th {
            background-color: #d32f2f;
            color: #ffffff;
            padding: 10px;
        }
        td {
            text-align: center;
            padding: 10px;
            color: #555;
        }
        tbody tr:nth-child(even) {
            background-color: #ffe0b2;
        }
        tbody tr:nth-child(odd) {
            background-color: #ffccbc;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #777;
        }
        /* Responsive Design */
        @media screen and (max-width: 768px) {
            table {
                font-size: 1em;
            }
            th, td {
                padding: 8px;
            }
            h2 {
                font-size: 1.5em;
            }
        }
        @media screen and (max-width: 480px) {
            table {
                font-size: 0.9em;
            }
            th, td {
                padding: 6px;
            }
            h2 {
                font-size: 1.2em;
            }
        }
    </style>
</head>
<body>
    <h2>🎄 MVOC Spiral Streeto - Christmas Edition 🎄</h2>
    <table>
        <thead>
            <tr>
                <th>P</th>
                <th>Competitor</th>
                <th>Longest Sequence</th>
                <th>Length</th>
                <th>Score</th>
                <th>Time</th>
                <th>Penalty</th>
                <th>Final Score</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $p_rank = 0;
                foreach ($competitorSequences as $result): 
                $p_rank += 1;?>
                <tr>
                    <td><?= $p_rank; ?></td>
                    <td><?= htmlspecialchars($result['name']) ?></td>
                    <td><?= htmlspecialchars(implode(", ", $result['sequence'])) ?></td>
                    <td><?= $result['length'] ?></td>
                    <td><?= htmlspecialchars($result['score']) ?></td>
                    <td><?= htmlspecialchars($result['time']) ?></td>
                    <td><?= htmlspecialchars($result['penalty']) ?></td>
                    <td><?= htmlspecialchars($result['final score']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <footer>
        🎅 Happy Holidays and Good Luck to All Competitors! 🎅
    </footer>
</body>
</html>
