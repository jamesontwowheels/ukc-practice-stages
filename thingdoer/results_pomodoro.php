<?PHP

include'dbconnect.php';


// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
else {
}

$today = date("Y-m-d");
$today2 = new DateTimeImmutable($today);
//$today =  date('Y-m-d',strtotime($today."+1 days"));
//$today = date('Y-m-d',strtotime($today."-1 days"));
$now = time(); // or your date as well
$check = "SELECT * from thingoer_thingdoer.track_table_pomodoro ORDER BY uid DESC";
$check_result = $conn->query($check);

echo "<h3>Good hours</h3>";

//echo "<table class='result_k'>";

        $d = 0;
        $mins_array = [0,0,0,0,0,0,0,0,0,0,0,0,0,0];
        $total_good_hours = 0;

        $hours_array = [
            [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
            [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
            [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
            [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
            [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
            [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
            [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
        ];

while ($checks = mysqli_fetch_assoc($check_result))
    { 
        $day = date('Y-m-d',strtotime($checks['entry_date']));
        $day2 = new DateTimeImmutable($day);
        $hour = $checks['hour'];
        $good = $checks['good'];


        if($good == 1){
            $total_good_hours += 1;
        }

        if($day == $today){ 
        }
        $dDiff = $day2->diff($today2);
       $this_day = $dDiff->format('%r%a');

        if($dDiff<7){
            $hours_array[$this_day][$hour] = $good;
        }

//        $your_date = strtotime($day);
 //       $datediff = $now - $your_date;
  //      $daydiff = round($datediff / (60 * 60 * 24));
    }
//echo "</table>";

$d = 0;
$good_hours = 1;
echo "<table class='card_table'>";
while ($d<7){
    echo "<tr>";
    $h = 9;
    while ($h<19){
  echo "<td>";
  if($hours_array[$d][$h]==1){
    echo "<div class='l'></div>";
    $good_hours += 1;
  } else {
    echo "<div class='m'></div>";
  }
  echo "</td>";
  $h += 1;
}
  $d += 1;
  echo "</tr>";
}
echo "</table>";

?>
<table class="card_table"><tr>
<td>
<p>Last 7 days</p>

<?PHP 
  echo "<h3>".$good_hours." good hours</h3>";
?>
</td>
<td>
<p>Total Good Hours</p>
<?PHP echo $total_good_hours; ?>
</td>
</tr></table>

