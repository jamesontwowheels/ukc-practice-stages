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


$check = "SELECT * from thingoer_thingdoer.track_table_2023 where goal_1 = 1 ORDER BY uid DESC";
$check_result = $conn->query($check);

$check2 = "SELECT * from thingoer_thingdoer.track_table_2023 where goal_1 = 2 ORDER BY uid DESC";
$check_result2 = $conn->query($check2);
echo "<h3>Treat tracker</h3>";
echo "<table class='result_k'>";

$d = 0;
$w = 0;
$h = 0;
$k_all = 0;
$j_all = 0;
$dates = [];
$dates2 = [];
while ($checks = mysqli_fetch_assoc($check_result))
    { 
        $dates [] = $checks['date'];
    }
while ($checks2 = mysqli_fetch_assoc($check_result2))
    { 
        $dates2 [] = $checks2['date'];
    }
echo "<tr>";
while ($w < 3){
while ($d < 7){
    echo "<td>";
    
    echo $h."<br>";
    $d += 1;
    $k = 0;
    $j = 0;
    while ($k < count(array_keys($dates, $today))){
        echo '<div class="k"></div>';
        $k += 1;
    };
    $k_all += $k;
    while ($j < 4-$k){
        echo '<div class="j"></div>';
        $j += 1;
    };
    $j_all += $j;
    $today = date('Y-m-d',strtotime($today."-1 days"));
    $h -= 1;
    echo "</td>";
}
$d = 0 ;
$w += 1 ;
echo "</tr><tr>";
}

echo " </tr></table>";