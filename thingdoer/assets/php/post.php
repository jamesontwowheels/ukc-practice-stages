<?PHP

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

//define the update

if ($purpose == 1){

  $sql = "INSERT INTO dbo.track_table (user_id, goal_1, date_int, purpose ) VALUES 
  ($user, 1, $led_1, $daysAfter, $purpose);";
$db_response[] = $sql;
if ($conn->query($sql) == TRUE) {
  $db_response[] =  "record inserted successfully";
  $last_id = $conn->insert_id;
  $db_response[] = "The success inserted ID is: " . $last_id . "<br>";
} else {
  $db_response[] = "Error: " . $sql . "<br>" . $conn->error;
  $db_response[] = "The last inserted ID is: " . $last_id . "<br>";
}

}

//send it to the db

