<?PHP

include 'assets/php/db_connect.php';

$led_1 = $_GET["led_1"];
$user = $_GET['user'];
$purpose = $_GET['purpose'];

echo 'purpose = ' . $purpose;

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

$table = 'thingdoer_inputs';

if($purpose == 1){

  try {
    // Assuming you already have a PDO connection named $pdo
    $sql = "INSERT INTO $table (Purpose, CreatedAt, InputValue,UserName) 
            VALUES (:purpose, :createdAt, :inputValue, :userName)";
    
    $stmt = $conn->prepare($sql);

    // Create a DateTime2 formatted timestamp
    $datetime2 = (new DateTime())->format('Y-m-d\TH:i:s.u') . '0';

    // Bind parameters securely
    $stmt->bindParam(':purpose', $led_1, PDO::PARAM_INT);
    $stmt->bindParam(':createdAt', $datetime2, PDO::PARAM_STR);
    $stmt->bindParam(':inputValue', $led_1, PDO::PARAM_INT);
    $stmt->bindParam(':userName', $user, PDO::PARAM_STR);

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
}