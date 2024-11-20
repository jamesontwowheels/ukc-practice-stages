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

    $longestSequence = [];
    $currentSequence = [];
    $expectedControlId = 10;

    foreach ($punches as $punch) {
        $controlId = (int)$punch['ControlId'];

        if ($expectedControlId === null || $controlId === $expectedControlId) {
            // Start a new sequence or continue the current one
            $currentSequence[] = $controlId;
            $expectedControlId = $controlId + 1; // Update the expected ControlId
        } elseif ($controlId > $expectedControlId) {
            // Missed the expected ControlId; save the current sequence if it's the longest
            if (count($currentSequence) > count($longestSequence)) {
                $longestSequence = $currentSequence;
            }
            // Reset the sequence and attempt to start from the current control
           // $currentSequence = [$controlId];
          //  $expectedControlId = $controlId + 1;
        }
        // Otherwise, skip this control and continue checking
    }

    // Final check after the loop
    if (count($currentSequence) > count($longestSequence)) {
        $longestSequence = $currentSequence;
    }

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

    // Store the result
    $competitorSequences[] = [
        'name' => $name,
        'sequence' => $longestSequence,
        'length' => count($longestSequence)
    ];
}

// Sort the array by the length of the sequence in descending order
usort($competitorSequences, function($a, $b) {
    return $b['length'] - $a['length'];
});

// Output the results as an HTML table
?>
<!DOCTYPE html>
<html>
<head>
    <title>Longest Consecutive ControlIDs</title>
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
            width: 70%;
            border-collapse: collapse;
            margin: 20px auto;
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
    </style>
</head>
<body>
    <h2>🎄 Longest Consecutive ControlIDs - Christmas Edition 🎄</h2>
    <table>
        <thead>
            <tr>
                <th>Competitor</th>
                <th>Longest Sequence</th>
                <th>Length</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($competitorSequences as $result): ?>
                <tr>
                    <td><?= htmlspecialchars($result['name']) ?></td>
                    <td><?= htmlspecialchars(implode(", ", $result['sequence'])) ?></td>
                    <td><?= $result['length'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <footer>
        🎅 Happy Holidays and Good Luck to All Competitors! 🎅
    </footer>
</body>
</html>
