<?php

try {
    // Connect to Azure SQL
    include 'db_connect.php';
    // Get `purpose` parameter from request
    $purpose = isset($_GET['purpose']) ? intval($_GET['purpose']) : 0;

    // Prepare and execute SQL query
    $stmt = $conn->prepare("SELECT * FROM thingdoer_inputs");
    //$stmt->bindParam(":purpose", $purpose, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch all results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    header("Content-Type: application/json");
    echo json_encode($results);

} catch (PDOException $e) {
    // Handle connection errors
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}

// Close connection
$conn = null;
?>
