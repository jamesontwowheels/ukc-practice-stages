<?php
echo "<h2>Environment Variables Test</h2>";

echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>Variable</th><th>Value</th></tr>";

// Loop through all environment variables
foreach ($_ENV as $key => $value) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($key) . "</td>";
    echo "<td>" . htmlspecialchars($value) . "</td>";
    echo "</tr>";
}

// Also check getenv() specifically
$testVar = getenv('DB_PASSWORD');
echo "<tr><td>getenv('DB_PASSWORD')</td><td>" . ($testVar !== false ? htmlspecialchars($testVar) : "NOT SET") . "</td></tr>";

echo "</table>";
?>
