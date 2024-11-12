<?PHP

session_start();
$servername = "localhost";
$username = "root";
$password = "root";
$db = "thingoer_thingdoer";

// Create connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
else {
}
?>