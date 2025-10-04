<?php
// PHP Data Objects(PDO) Sample Code:
try {
    $conn = new PDO("sqlsrv:server = tcp:aarc-server.database.windows.net,1433; Database = aarc_db", "aarc_admin", getenv('DB_PASSWORD'));
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $debug_log[] = "db-connected ";
}
catch (PDOException $e) {
    $debug_log[] = 'broken';
    print("Error connecting to SQL Server.");
    die(print_r($e));
}
