<?PHP

include 'dbconnect.php';

$led_1 = $_GET["led_1"];
$user = $_GET['user'];
$purpose = $_GET['purpose'];

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

$table = 'track_table_2023';

if($purpose == 1){
date_default_timezone_set("Europe/London");
$hour = date("H");
echo $hour;

$date = date("Y-m-d");

$insert = "INSERT INTO $table (`uid`,`goal_1`,`date`,`user_id`) VALUES (NULL, $led_1,'$date','$user')";
echo $insert;
if ($result = $conn->query($insert) === TRUE){
    echo json_encode($insert);
  }
  else {
  echo json_encode("Error: " . $query . "<br>" . $conn->error);
 }
}

elseif($purpose == 3){
  include 'function_undo.php';
}
