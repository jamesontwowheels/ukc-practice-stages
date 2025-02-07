<?php

include "db_connect.php";
// Assuming you already have a PDO connection in $conn
try {
    // Prepare the SQL query to fetch widgets from the database
    $sql = "SELECT UID, Type, Name, Description, Goal, User, widget_ID FROM thingdoer_widgets";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Fetch all rows as associative array
    $widgets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If no widgets are found, return an empty array
    if (!$widgets) {
        $widgets = [];
    }

    // Set the header to tell the browser it's JSON
    header('Content-Type: application/json');

    // Return the JSON-encoded widgets array
    echo json_encode($widgets);

} catch (PDOException $e) {
    // In case of an error, return a 500 error with the error message
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
