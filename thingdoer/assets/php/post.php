<?PHP

//connect to db
include 'db_connect.php';

//get the inputs
$inputValue = $_GET["InputValue"];
$userName = $_GET['user'];
$purpose = (int) $_GET['purpose'];

echo $purpose + 1;

$sql = "SELECT Widget FROM dbo.thingdoer_buttons WHERE Button = :purpose";
$stmt = $conn->prepare($sql);
echo $sql;
$stmt->bindValue(':purpose', $purpose, PDO::PARAM_INT);

$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
var_dump($row);

if ($row) {
    // Access the value with the correct key 'Widget' (capitalized)
    $widget_ID = $row['Widget'];  // Use 'Widget' instead of 'widget'
    echo "Widget ID: " . $widget_ID;
} else {
    echo "No result found.".$purpose;
}


//create a date
$createdAt = (new DateTime())->format('Y-m-d\TH:i:s'); // Format for DATETIME2

//define the update

$sql = "INSERT INTO dbo.thingdoer_inputs (widget_ID, CreatedAt, InputValue, UserName) VALUES (:widget_ID, :createdAt, :inputValue, :userName)";
$stmt = $conn->prepare($sql);

$stmt->bindParam(':widget_ID', $widget_ID);
$stmt->bindParam(':createdAt', $createdAt); // Correctly formatted datetime
$stmt->bindParam(':inputValue', $inputValue);
$stmt->bindParam(':userName', $userName);

$stmt->execute();