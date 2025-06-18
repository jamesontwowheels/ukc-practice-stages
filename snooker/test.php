<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = $_POST['punches'] ?? '';
    $lines = explode("\n", trim($rawInput));
    $punches = [];
    $time = 0;
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') continue;
        $controlId = (int)preg_replace('/[^0-9]/', '', $line);
        $punches[] = [
            'ControlId' => $controlId,
            'TimeAfterStartSecs' => $time
        ];
        $time += 10;
    }

    function findSnookerScore($punches) {
        usort($punches, fn($a, $b) => $a['TimeAfterStartSecs'] - $b['TimeAfterStartSecs']);

        $reds = range(1, 15);
        $colours = [20 => 2, 30 => 3, 40 => 4, 50 => 5, 60 => 6, 70 => 7];
        $colourSequence = array_keys($colours);

        $visitedReds = [];
        $score = 0;
        $sequence = [];

        $state = "main";
        $lastWasRed = false;
        $finalStageStarted = false;
        $finalColourIndex = 0;
        $postRedColourGiven = false;

        foreach ($punches as $punch) {
            $id = (int)$punch['ControlId'];
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
                    }
                    $lastWasRed = false;
                }
            }

            elseif ($state === "post-red-colour") {
                if (in_array($id, $reds)) {
                    continue; // ignore reds after 15
                } elseif (array_key_exists($id, $colours)) {
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
            }

            elseif ($state === "final") {
                if (!array_key_exists($id, $colours)) {
                    continue; // skip invalid
                }

                $expectedId = $colourSequence[$finalColourIndex] ?? null;

                if ($id === $expectedId) {
                    $score += $colours[$id];
                    $finalColourIndex++;
                } elseif ($expectedId !== null && $id < $expectedId) {
                    continue; // too early — ignore
                } elseif ($expectedId !== null && $id > $expectedId) {
                    $score -= 4; // too late — penalty
                }
            }
        }

        return [
            'sequence' => $sequence,
            'reds' => count($visitedReds),
            'score' => $score
        ];
    }

    $result = findSnookerScore($punches);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Snooker Score Validator</title>
    <style>
        body {
            font-family: sans-serif;
            max-width: 700px;
            margin: 40px auto;
            background: #f4f4f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        textarea {
            width: 100%;
            height: 200px;
            font-family: monospace;
            padding: 10px;
        }
        pre {
            background: #fff;
            padding: 10px;
            border: 1px solid #ccc;
        }
        button {
            padding: 10px 20px;
            background: #4caf50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h2>Snooker Score Validator</h2>
    <form method="post">
        <label for="punches">Enter control IDs (one per line):</label><br>
        <textarea name="punches" id="punches"><?php echo htmlspecialchars($_POST['punches'] ?? ""); ?></textarea><br><br>
        <button type="submit">Validate</button>
    </form>
    <?php if (isset($result)): ?>
        <h3>Results</h3>
        <pre><?php echo print_r($result, true); ?></pre>
    <?php endif; ?>
</body>
</html>

