<?PHP

//connect to db
include 'db_connect.php';

//get the inputs
$inputValue = $_GET["InputValue"];
$userName = $_GET['user'];
$purpose = $_GET['purpose'];

//create a date
$createdAt = (new DateTime())->format('Y-m-d H:i:s'); // Format date as 'YYYY-MM-DD HH:MM:SS'


//define the update
$sql = "INSERT INTO dbo.thingdoer (Purpose, CreatedAt, InputValue, UserName) VALUES 
    ($purpose, $createdAt, $inputValue, $userName);";
  
echo $sql; 
if ($conn->query($sql) == TRUE) {
    echo "record inserted successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    echo "The last inserted ID is: " . $last_id . "<br>";
}