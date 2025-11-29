<?php
// PHP Data Objects(PDO) Sample Code:
try {
    static $conn = null;
    if ($conn === null) {
    $conn = new PDO("sqlsrv:server = tcp:aarc-server.database.windows.net,1433; Database = aarc_db",
        "aarc_admin", getenv('DB_PASSWORD'), 
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
}
}
catch (PDOException $e) {
    $debug_log[] = 'broken';
    //print("Error connecting to SQL Server.");
    die($debug_log[] = $e);
}
