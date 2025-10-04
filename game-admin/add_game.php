<?php
require '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $game_number   = $_POST['game_number'] ?? null;
    $location_name = $_POST['location_name'] ?? null;
    $game_date     = $_POST['game_date'] ?? null;
    $game_rules    = $_POST['game_rules'] ?? 1;
    $action        = $_POST['action'] ?? 'dryrun';

    if (!$game_number || !$location_name) {
        die("❌ Missing required fields.");
    }

    // --- 1. Handle KML Upload ---
    if (!isset($_FILES['kml_file']) || $_FILES['kml_file']['error'] !== UPLOAD_ERR_OK) {
        die("❌ Error uploading KML file.");
    }

    $kmlPath = $_FILES['kml_file']['tmp_name'];
    $xml = simplexml_load_file($kmlPath);
    if (!$xml) {
        die("❌ Invalid KML file.");
    }

    $xml->registerXPathNamespace('kml', 'http://www.opengis.net/kml/2.2');
    $placemarks = $xml->xpath('//kml:Placemark');
    $features = [];

    foreach ($placemarks as $pm) {
        $name = (string)$pm->name;
        $coords = (string)$pm->Point->coordinates;

        if ($coords) {
            $parts = explode(',', trim($coords));
            $lon = floatval($parts[0]);
            $lat = floatval($parts[1]);
            $alt = isset($parts[2]) ? floatval($parts[2]) : 0;

            $features[] = [
                "type" => "Feature",
                "geometry" => [
                    "type" => "Point",
                    "coordinates" => [$lon, $lat, $alt]
                ],
                "properties" => [
                    "name" => $name
                ]
            ];
        }
    }

    $featuresJson = json_encode($features, JSON_PRETTY_PRINT);

    // --- 2. Validation ---
    try {
        $stmt = $conn->prepare("SELECT mandatory_points FROM games_reference_data WHERE game_number = :gn");
        $stmt->bindParam(':gn', $game_number, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $mandatory = [];
        if ($row && !empty($row['mandatory_points'])) {
            $mandatory = json_decode($row['mandatory_points'], true);
        }

        $present = array_map(function($f) {
            return is_numeric($f['properties']['name']) ? (int)$f['properties']['name'] : $f['properties']['name'];
        }, $features);

        $missing = array_diff($mandatory, $present);

    } catch (PDOException $e) {
        die("❌ DB Error (validation): " . $e->getMessage());
    }

    // --- 3. Auto-generate next available location_number ---
    try {
        $stmt = $conn->prepare("SELECT MAX(location_number) AS max_loc FROM games WHERE game_number = :gn");
        $stmt->bindParam(':gn', $game_number, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $location_number = ($row && $row['max_loc']) ? $row['max_loc'] + 1 : 1;
    } catch (PDOException $e) {
        die("❌ DB Error (location number): " . $e->getMessage());
    }

    // --- 4. Dry-run or Insert ---
    if ($action === 'dryrun') {
        echo "<h2>Dry-Run Results</h2>";

        echo "Proposed Location Number: <b>{$location_number}</b><br><br>";

        if (!empty($missing)) {
            echo "❌ Missing mandatory features: <b>" . implode(', ', $missing) . "</b><br><br>";
        } else {
            echo "✅ All mandatory features are present.<br><br>";
        }

        echo "<h3>Extracted Features JSON:</h3>";
        echo "<pre>" . htmlspecialchars($featuresJson) . "</pre>";
        echo "<br><a href='add_game_form.php'>Back to Add Game</a>";

    } elseif ($action === 'add') {
        if (!empty($missing)) {
            die("❌ Cannot add game. Missing mandatory features: " . implode(', ', $missing));
        }

        try {
            $sql = "INSERT INTO games (game_number, location_number, location_name, game_date, game_rules, features)
                    VALUES (:game_number, :location_number, :location_name, :game_date, :game_rules, :features)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':game_number', $game_number, PDO::PARAM_INT);
            $stmt->bindParam(':location_number', $location_number, PDO::PARAM_INT);
            $stmt->bindParam(':location_name', $location_name, PDO::PARAM_STR);
            $stmt->bindParam(':game_date', $game_date, PDO::PARAM_STR);
            $stmt->bindParam(':game_rules', $game_rules, PDO::PARAM_INT);
            $stmt->bindParam(':features', $featuresJson, PDO::PARAM_STR);
            $stmt->execute();

            echo "✅ Game added successfully.<br>";
            echo "Game Number: <b>{$game_number}</b><br>";
            echo "Location Number: <b>{$location_number}</b><br><br>";
            echo "<a href='view_games.php'>View Games</a>";

        } catch (PDOException $e) {
            die("❌ Insert Error: " . $e->getMessage());
        }
    }
}
?>
