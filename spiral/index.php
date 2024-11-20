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
    $expectedControlId = null;

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
            $currentSequence = [$controlId];
            $expectedControlId = $controlId + 1;
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
    $competitorSequences[$name] = $longestSequence;
}

// Output the results as an HTML table
?>
<!DOCTYPE html>
<html>
<head>
    <title>Longest Consecutive ControlIDs</title>
    <style>
        table {
            width: 50%;
            border-collapse: collapse;
            margin: 20px auto;
            font-family: Arial, sans-serif;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Longest Consecutive ControlIDs by Competitor</h2>
    <table>
        <thead>
            <tr>
                <th>Competitor</th>
                <th>Longest Sequence</th>
                <th>Length</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($competitorSequences as $name => $sequence): ?>
                <tr>
                    <td><?= htmlspecialchars($name) ?></td>
                    <td><?= htmlspecialchars(implode(", ", $sequence)) ?></td>
                    <td><?= count($sequence) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
