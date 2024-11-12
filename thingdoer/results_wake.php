<?PHP

include'dbconnect.php';


// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
else {
}

$today = date("Y-m-d");
$today =  date('Y-m-d',strtotime($today."+1 days"));
$today = date('Y-m-d',strtotime($today."-1 days"));

$now = time(); // or your date as well

$check = "SELECT * from thingoer_thingdoer.track_table_wake ORDER BY uid DESC";
$check_result = $conn->query($check);

echo "<h3>Earned time</h3>";

//echo "<table class='result_k'>";

$d = 0;
$mins_array = [0,0,0,0,0,0,0,0,0,0,0,0,0,0];
$total_mins = 0;
while ($checks = mysqli_fetch_assoc($check_result))
    { 
        $mins = max(0,450 - $checks['wake']);
        $wake_hour = floor($checks['wake']/60);
        $wake_min = $checks['wake'] - 60*$wake_hour;
        $day = $checks['entry_date'];
        // echo "<tr><td>Wake: $wake_hour : $wake_min</td><td>$mins</td><td>$day</td></tr>";
        
        $total_mins += $mins;
        $your_date = strtotime($day);
        $datediff = $now - $your_date;
        $daydiff = round($datediff / (60 * 60 * 24));
        if($daydiff<14){
        $mins_array[$daydiff] = $mins;
        }
    }
//echo "</table>";

$d = 0;
echo "<table class='card_table'><tr>";
while ($d<7){
  echo "<td>";
  if($mins_array[$d]>0){
    echo "<div class='l'></div>";
  } else {
    echo "<div class='m'></div>";
  }
  echo "</td>";
  $d += 1;
}
echo "</tr></table>";

$seven_day_total = array_slice($mins_array, 0, 7);
$seven_day_hours = floor(array_sum($seven_day_total)/60);
$seven_day_mins = array_sum($seven_day_total) - 60*$seven_day_hours;
$previous_seven_day_total = array_slice($mins_array, 7, 7);
$seven_day_difference = array_sum($seven_day_total)-array_sum($previous_seven_day_total);
$total_days = 0;
$total_days = floor($total_mins/(60*24));
$total_hours = floor(($total_mins-($total_days*60*24))/60);
$total_minutes = ($total_mins - $total_hours*60 - $total_days*60*24);

?>
<table class="card_table"><tr>
<td>
<p>Last 7 days</p>
<h3><?PHP echo $seven_day_hours."h ".$seven_day_mins."m";?> </h3>
<?PHP 
if($seven_day_difference >0){
  echo '<div class="triangle-up"></div>';
  echo "<p>".$seven_day_difference."m</p>";
} elseif($seven_day_difference<0){

  echo "<p>".$seven_day_difference."m</p>";
  echo '<div class="triangle-down"></div>';
}
?>
</td>
<td>
<p>Total</p>
<?PHP echo $total_days."d ".$total_hours."h ".$total_minutes."m"; ?>
</td>
</tr></table>

