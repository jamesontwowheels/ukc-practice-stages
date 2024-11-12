<?PHP

include 'dbconnect.php';

$led_1 = $_GET["led_1"];
$user = $_GET['user'];

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
date_default_timezone_set("Europe/London");
$hour = date("H");
echo $hour;
if($hour > 4 && $hour < 14){
	$purpose = 1;
}
else {$purpose = 2;}


$date = date("Y-m-d");

$check = "SELECT * from track_table where date = '$date' and user_id = $user";
$check_result = $conn->query($check);

$UID = -1;
while ($checks = mysqli_fetch_assoc($check_result))
{ $UID = $checks['uid'];}

if ($UID == -1){
  if ($purpose == 2){
  $insert = "INSERT INTO track_table (`uid`,`goal_1`,`date`,`purpose`,`user_id`) VALUES (NULL, $led_1,'$date','$purpose','$user')";
  }
  else {
  $insert = "INSERT INTO track_table (`uid`,`aim_1`,`date`,`purpose`,`user_id`) VALUES (NULL, $led_1,'$date','$purpose','$user')";  
  }
}
else {
  if ($purpose == 2){
    $insert = "UPDATE track_table SET goal_1 = $led_1 WHERE `track_table`.`uid` = $UID";
  }
  else{
    $insert = "UPDATE track_table SET aim_1 = $led_1 WHERE `track_table`.`uid` = $UID";
  }
}

 if ($result = $conn->query($insert) === TRUE){
   echo json_encode($insert);
 }
 else {
 echo json_encode("Error: " . $query . "<br>" . $conn->error);
}

?>
