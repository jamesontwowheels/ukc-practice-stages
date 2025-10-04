<?php
try {
    $serverName = "tcp:aarc-server.database.windows.net,1433";
    $database   = "aarc_db";
    $username   = "aarc_admin";
    $password   = getenv('DB_PASSWORD');

    if (!$password) {
        throw new Exception("Environment variable DB_PASSWORD not set or empty.");
    }

    $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}
catch (Exception $e) {
    echo "❌ Error connecting to SQL Server: " . htmlspecialchars($e->getMessage());
}
catch (PDOException $e) {
    echo "❌ PDO Error: " . htmlspecialchars($e->getMessage());
}
?>
