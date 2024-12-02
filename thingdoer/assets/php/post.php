<?PHP

//connect to db
include 'db_connect.php';

//get the inputs
$inputValue = $_GET["InputValue"];
$userName = $_GET['user'];
$purpose = $_GET['purpose'];

//create a date
$createdAt = (new DateTime())->format('Y-m-d\TH:i:s'); // Format for DATETIME2


//define the update

$sql = "INSERT INTO dbo.thingdoer (Purpose, CreatedAt, InputValue, UserName) VALUES (:purpose, :createdAt, :inputValue, :userName)";
$stmt = $conn->prepare($sql);

$stmt->bindParam(':purpose', $purpose);
$stmt->bindParam(':createdAt', $createdAt); // Correctly formatted datetime
$stmt->bindParam(':inputValue', $inputValue);
$stmt->bindParam(':userName', $userName);

$stmt->execute();