<?php
// Define the URL to fetch data from
$url = "https://p.fne.com.au:8886/resultsGetPublicForEvent?eventName=snooker-o%20PXAS%20ScoreV60";
$local_test = 0;
if ($local_test == 1) {$url = "results_test.json";}
// Fetch data from the URL
$response = file_get_contents($url);
if ($response === FALSE) die("Error fetching data.");
$data = json_decode($response, true);
if ($data === NULL || !isset($data['results'])) die("Invalid JSON structure.");

function formatTime($seconds) {
    if ($seconds >= 3600) {
        return gmdate("H:i:s", $seconds);
    }
    return gmdate("i:s", $seconds);
}

function extractControlId($control) {
    if (isset($control['ControlId'])) {
        return (int) filter_var($control['ControlId'], FILTER_SANITIZE_NUMBER_INT);
    }
    return 0;
}

function findSnookerScore($punches, $timeTakenSecs) {
    usort($punches, fn($a, $b) => $a['TimeAfterStartSecs'] - $b['TimeAfterStartSecs']);

    $reds = range(1, 15);
    $colours = [20 => 2, 30 => 3, 40 => 4, 50 => 5, 60 => 6, 70 => 7];
    $colourSequence = array_keys($colours);

    $visitedReds = [];
    $score = 0;
    $sequence = [];
    $penaltyPoints = 0;

    $state = "main";
    $lastWasRed = false;
    $finalColourIndex = 0;
    $postRedColourGiven = false;

    $lastControlId = null;

    foreach ($punches as $punch) {
        $id = extractControlId($punch);
        if ($id === $lastControlId) continue; // skip if same as previous control
        $lastControlId = $id;

        $sequence[] = $id;

        if ($state === "main") {
            if (in_array($id, $reds) && !in_array($id, $visitedReds)) {
                $score += 1;
                $visitedReds[] = $id;
                $lastWasRed = true;

                if (count($visitedReds) === 15) {
                    $state = "post-red-colour";
                }
            } elseif (array_key_exists($id, $colours)) {
                if ($lastWasRed) {
                    $score += $colours[$id];
                } else {
                    $score -= 4;
                    $penaltyPoints += 4;
                }
                $lastWasRed = false;
            }
        } elseif ($state === "post-red-colour") {
            if (in_array($id, $reds)) continue;
            elseif (array_key_exists($id, $colours)) {
                if (!$postRedColourGiven) {
                    $postRedColourGiven = true;

                    if ($id == 20) {
                        $score += $colours[$id];
                        $finalColourIndex = 1;
                    } else {
                        $score += $colours[$id];
                        $finalColourIndex = 0;
                    }
                    $state = "final";
                }
            }
        } elseif ($state === "final") {
            if (!array_key_exists($id, $colours)) continue;
            $expectedId = $colourSequence[$finalColourIndex] ?? null;

            if ($id === $expectedId) {
                $score += $colours[$id];
                $finalColourIndex++;
            } elseif ($expectedId !== null && $id < $expectedId) {
                continue;
            } elseif ($expectedId !== null && $id > $expectedId) {
                $score -= 4;
                $penaltyPoints += 4;
            }
        }
    }

    // Late penalty
    $lateMinutes = ceil(max(0, $timeTakenSecs - 3600) / 60);
    $latePenalty = $lateMinutes * 4;
    $score -= $latePenalty;
    $penaltyPoints += $latePenalty;

    return [
        'sequence' => $sequence,
        'reds' => count($visitedReds),
        'score' => $score,
        'penalties' => $penaltyPoints,
        'time' => $timeTakenSecs
    ];
}

$manualOverride = [
    'Tim Scarbrough' => [13, 60,12, 60, 11, 50, 10, 50, 8, 40, 9 , 40, 1, 30, 2, 70, 14, 70, 15, 70, 7, 70, 6, 70, 4, 5, 20, 30, 40, 50, 60, 70]
];

$results = [];
foreach ($data['results'] as $participant) {
    $name = $participant['Firstname'] . " " . $participant['Surname'];
    $punches = $participant['Punches'] ?? [];
    $timeTakenSecs = $participant['TotalTimeSecs'] ?? 0;

    if (isset($manualOverride[$name])) {
        $manualPunches = array_map(fn($id, $i) => ["ControlId" => (string)$id, "TimeAfterStartSecs" => $i], $manualOverride[$name], array_keys($manualOverride[$name]));
        $res = findSnookerScore($manualPunches, $timeTakenSecs);
    } else {
        $res = findSnookerScore($punches, $timeTakenSecs);
    }

    $results[] = [
        'name' => $name,
        'sequence' => implode(", ", $res['sequence']),
        'score' => $res['score'],
        'penalties' => $res['penalties'],
        'time' => formatTime($res['time'])
    ];
}

usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Snooker StreetO Results</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="10">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #222;
            color: #eee;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #f44336;
            font-size: 2em;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #333;
            box-shadow: 0 0 10px rgba(0,0,0,0.6);
            font-size: 1em;
        }
        th, td {
            padding: 10px;
            border: 1px solid #555;
            text-align: center;
        }
        th {
            background: #444;
            color: #f1c40f;
        }
        tr:nth-child(even) {
            background: #2c2c2c;
        }
        tr:nth-child(odd) {
            background: #1f1f1f;
        }
        footer {
            text-align: center;
            color: #aaa;
            margin-top: 20px;
        }

        @media screen and (max-width: 768px) {
            body {
                padding: 10px;
            }
            table, th, td {
                font-size: 0.9em;
                padding: 8px;
            }
            h2 {
                font-size: 1.5em;
            }
        }

        @media screen and (max-width: 480px) {
            table, th, td {
                font-size: 0.8em;
                padding: 6px;
            }
            h2 {
                font-size: 1.2em;
            }
        }
    </style>
</head>
<body>
<h2>🏆 Snooker StreetO Results 🏆</h2>
<table>
    <thead>
        <tr>
            <th>Pos</th>
            <th>Player</th>
            <th>Punch Sequence</th>
            <th>Score</th>
            <th>Penalty Points</th>
            <th>Time</th>
        </tr>
    </thead>
    <tbody>
        <?php $rank = 1; foreach ($results as $r): ?>
            <tr>
                <td><?= $rank++ ?></td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= htmlspecialchars($r['sequence']) ?></td>
                <td><?= $r['score'] ?></td>
                <td><?= $r['penalties'] ?></td>
                <td><?= $r['time'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<footer>🌝 Powered by Snooker StreetO Scoring 🎯</footer>
</body>
</html>