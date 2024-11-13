<?PHP

include 'db_connect.php';

//identify your incoming variables
$led_1 = $_GET["led_1"];
$user = $_GET['user'];
$purpose = $_GET['purpose'];

//identify your target table
$table = 'track_table_pomodoro';

if($purpose == 1 || $purpose == 2){

/** Define your insert criteria*/
date_default_timezone_set("Europe/London");
$hour = date("H");
$minute = date("i");
echo $minute;
if($minute < 15){
   $hour -= 1;
}

$check = "SELECT * from $table ORDER BY uid DESC";
$check_result = $conn->query($check);
$date = date("Y-m-d");
$block = 0;

while ($checks = mysqli_fetch_assoc($check_result))
    { 
        if($checks['entry_date']==$date){
            if($checks['hour']==$hour){
                $block = 1;
            }
        };
    }
if($block == 1){
    echo "already inserted";
} else {
$insert = "INSERT INTO $table (`uid`,`entry_date`,`user_id`,`good`,`hour`) VALUES (NULL, '$date','$user','$purpose','$hour')";
echo $insert;


//do the insert and check for success
if ($result = $conn->query($insert) === TRUE){
    echo json_encode($insert);
  }
  else {
  echo json_encode("Error: " . $query . "<br>" . $conn->error);
 }
}
}

elseif($purpose == 3){
  include 'function_undo.php';
}
