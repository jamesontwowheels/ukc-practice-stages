<?PHP

include 'assets/php/db_connect.php';

$button_ID = $_GET["button_ID"];

/* $servername = "localhost";
$username = "root";
$password = "root";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";
*/

$table = 'thingdoer_buttons';

  try {
    // Assuming you already have a PDO connection named $pdo
    $sql = "SELECT widget FROM $table WHERE UID = :button_ID";
    
    $stmt = $conn->prepare($sql);

    // Bind parameters securely
    $stmt->bindParam(':button_ID', $button_ID, PDO::PARAM_INT);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Record inserted successfully!";
    } else {
        echo "Error: Could not insert record.";
    }
} catch (PDOException $e) {
    // Log or display error message
    echo "Database Error: " . $e->getMessage();
}
