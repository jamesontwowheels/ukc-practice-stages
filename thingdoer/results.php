<?PHP

include'dbconnect.php';


// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
else {
}
$today = date("Y-m-d");

$check = "SELECT * from thingoer_thingdoer.track_table ORDER BY uid DESC";
$check_result = $conn->query($check);

$purpose_array = ["null","aim","result"];

echo "<table>";

$s1 = [0,0];
$s2 = [0,0];
$s3 = [0,0];

$m1 = [0,0];
$m2 = [0,0];
$m3 = [0,0];

while ($checks = mysqli_fetch_assoc($check_result))
{ 
	echo "<tr>";
	$row = [];
	$g1 = $checks['goal_1'];
	$g2 = $checks['goal_2'];
	$g3 = $checks['goal_3'];
	
	//calculate Streaks
	//Streak 1
    	echo "<td>";
    	if($g1 == 1 && $s1[0] == 0)
    	{ $s1[1] += 1; 	}
    	elseif( $g1 === "0") { $s1[0] = 1;};
    //Streak 2
    	if(($g2 == 1) && ($s2[0] == 0))
    	{ $s2[1] += 1; }
    	elseif( $g2 === "0") { $s2[0] = 1;};
    //Streak 3    
    	if(($g3 == 1) && ($s3[0] == 0))
    	{ $s3[1] += 1; }
    	elseif( $g3 === "0") { $s3[0] = 1;
    	};
	
	//calculate imperfect streaks
	//Streak 1
    	if(($g1 == 1) && ($m1[0] < 2))
    	{ $m1[1] += 1; 
    	    $m1[0] = 0;
    	}
    	elseif( $g1 === "0") { $m1[0] += 1;};
    	
    	
    //Streak 2
    	if(($g2 == 1) && ($m2[0] < 2))
    	{ $m2[1] += 1; 
    	    $m2[0] = 0;
    	}
    	elseif( $g2 === "0") { $m2[0] += 1;};
    //Streak 3    
    	if(($g3 == 1) && ($m3[0] < 2))
    	{ $m3[1] += 1; 
    	    $m3[0] = 0;
    	}
    	elseif( $g3 === "0") { $m3[0] += 1;};
    	
    	
	$a1 = $checks['aim_1'];
	$a2 = $checks['aim_2'];
	$a3 = $checks['aim_3'];
	$date = $checks['date'];
	$purpose = $purpose_array[$checks['purpose']];
	
	$row[] = $date;
	$row[] = $purpose;
	$row[] = $g1;
	$row[] = $g2;
	$row[] = $g3;
	$row[] = "<i>goal:</i>";
	$row[] = $a1;
	$row[] = $a2;
	$row[] = $a3;
	
	$print = [];
	
	$p1 = "";
	//goal 1 - this will get tidier, probably
	$p1 = strval($a1) . strval($g1);
	$p2 = strval($a2) . strval($g2);
	$p3 = strval($a3) . strval($g3);
	
	$print[] = $p1;
	$print[] = $p2;
	$print[] = $p3;
	
	$x = 0;
	
	//update to $x < 9 for debugging ;-) 
	while ($x < 1){
		echo "<td>";
		echo "<div class=''>";
		echo $row[$x];
		echo "</div>";
		echo "</td>";
		$x += 1;
	}
	
	$y = 0;
	while ($y < 3){
		echo "<td>";
		echo "<div class='blob blob_".$print[$y]."'>";
		echo "</div>";
		echo "</td>";
		$y += 1;
	}
	
	echo "</tr>";
		
}

echo "</table>";
?>



<h3>Streak 1 = <?PHP echo $s1[1]; ?></h3>
<h3>Streak 2 = <?PHP echo $s2[1]; ?></h3>

<h3>Streak 3 = <?PHP echo $s3[1]; ?></h3>

<h3>Imperfect streak 1 = <?PHP echo $m1[1]; ?></h3>
<h3>Imperfect streak 2 = <?PHP echo $m2[1]; ?></h3>
<h3>Imperfect streak 3 = <?PHP echo $m3[1]; ?></h3>