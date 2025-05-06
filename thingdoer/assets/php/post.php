<?PHP

//connect to db
include 'db_connect.php';

//get the inputs
$inputValue = $_GET["InputValue"];
$userName = $_GET['user'];
$purpose = $_GET['purpose'];

$query = "SELECT widget FROM dbo.thingdoer_buttons WHERE button = ? LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $purpose);
$stmt->execute();
$stmt->bind_result($widget);
if ($stmt->fetch()) {
    $widget_ID = $widget;
} else {
    echo "No result found.";
}
$stmt->close();


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