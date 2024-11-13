<?php

//connect to db
include 'db_connect.php';

//get the inputs
$led_1 = $_GET["led_1"];
$user = $_GET['user'];
$purpose = $_GET['purpose'];

//create a date
date_default_timezone_set("Europe/London");
$startDate = new DateTime("2024-11-01");
// Get today's date
$today = new DateTime("now");
// Calculate the difference in days
$interval = $startDate->diff($today);
$daysAfter = $interval->days;
echo "days after $daysAfter";
//define the update
if ($purpose == 1) {
    // Using a prepared statement to avoid SQL injection
    $sql = "INSERT INTO dbo.track_table (user_id, goal_1, date_int, purpose) VALUES (:user, :led_1, :daysAfter, :purpose)";
    $stmt = $conn->prepare($sql);
    echo $sql;
    // Bind parameters
    $stmt->bindParam(':user', $user, PDO::PARAM_INT);
    $stmt->bindParam(':led_1', $led_1, PDO::PARAM_INT);
    $stmt->bindParam(':daysAfter', $daysAfter, PDO::PARAM_INT);
    $stmt->bindParam(':purpose', $purpose, PDO::PARAM_INT);

    // Execute and check for success
    if ($stmt->execute()) {
        $last_id = $conn->lastInsertId();
        $db_response[] = "Record inserted successfully";
        $db_response[] = "The success inserted ID is: " . $last_id . "<br>";
    } else {
        $db_response[] = "Error: " . $conn->errorInfo()[2];
    }
}
?>