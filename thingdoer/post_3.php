/* track wake up time */

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

$table = 'track_table_wake';

if($purpose == 1){
date_default_timezone_set("Europe/London");
$hour = date("H");
$minute = date("i");
$waketime = $hour * 60 + $minute;
echo $waketime;

$date = date("Y-m-d");

$check = "SELECT * from $table ORDER BY uid DESC";
$check_result = $conn->query($check);


$block = 0;
while ($checks = mysqli_fetch_assoc($check_result))
    { 
        if($checks['entry_date']==$date){
            $block = 1;
        };
    }

if($block == 0){
$insert = "INSERT INTO track_table_wake (`uid`,`wake`,`entry_date`,`user_id`) VALUES (NULL, $waketime,'$date','$user')";
echo $insert;
if ($result = $conn->query($insert) === TRUE){
    echo json_encode($insert);
  }
  else {
  echo json_encode("Error: " . $query . "<br>" . $conn->error);
 }
}
else {
    echo "already submitted";
}
}

elseif($purpose==3){

    include 'function_undo.php';

}
