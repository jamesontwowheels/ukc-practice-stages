<?PHP

include'dbconnect.php';


// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
else {
}
$today = date("Y-m-d");
$check = "SELECT * from thingoer_thingdoer.track_table WHERE`user_id` = $user_id order by date asc";
$check_result = $conn->query($check);

$purpose_array = ["null","aim","result"];


echo "<table class='weekly'>";

$s1 = [0,0];
$s2 = [0,0];
$s3 = [0,0];

$m1 = [0,0];
$m2 = [0,0];
$m3 = [0,0];
$day = 0;

echo "<tr>";

while ($checks = mysqli_fetch_assoc($check_result))
{ 

    if($day === 5){
        echo '</tr><tr>';
        $day=0;
    }
    $a = 0 + $checks['aim_1'];
    $b = 0 + $checks['goal_1'];
    $result = $a.$b;
    $date = $checks['date'];    
    $datetime = new DateTime($date);
    $cal_day = $datetime->format('d');
echo $newformat;
    echo "<td><div class='blob blob_$result'></div><p>$cal_day</p></td>";
    $day = $day + 1;
		
}

echo "</tr></table>";
?>