<head>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">  
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="magic.js"></script>

</head>

<body>

<?php

/*
CP 1 = I
CP 2 = E
CP 3 = K
CP 4 = 7
CP 5 = W
CP 6 = L
CP 7 = N
CP 99 = Full stop
CP S1/F1 = S/F
CP 11 = PP1
CP 12 = PP2
...
*/

$words = ["TWINKLE","WINKLE","WELKIN","TINKLE","WINTLE","WELKT","TWINK","LIKEN","INKLE","KNELT","TWINE","INLET","ENLIT","ELINT","INTEL","LENTI"];
$results = [];

$file = fopen('results3.csv', 'r');
while (($line = fgetcsv($file)) !== FALSE) {
  
$w = "";            //word
$l = 0;             //length of word
$SF = 0;            //start finish
$PM = 1;            //puzzle multiplier
$PU = [0,0,0,0];    //puzzles used
$PV = [2,2,2,2];    //puzzle value
$word = ["","I","E","K","T","W","L","N"];
$x = 0;
$y = 46;
$CPS = [];
$times = [];
$used_words = [];
$score = 0;
$buy_words = 0;

echo "<h3>";
$name = $line[4]." ".$line[3];
echo $name;
echo "</h3>";

echo "<table>";

while ($y <= count ($line)){
    if(strlen($line[$y]) > 0){
    $z = $y +1;
    $CPS[] = preg_replace("/[^0-9]/", "", $line[$y]);
    $times[] = strtotime($line[$z]);
    }
    $y += 2;

}
array_multisort($times, $CPS);

while( $x <= count($CPS)){

    $CP = $CPS[$x];

    //add a letter
    if ($CP < 8){
        $w = $w . $word[$CP];
        $l += 1;
    };

    //add a puzzle-point
    if( $CP > 9  && $CP < 20){
        $PP = $CP - 11;
        if($PU[$PP] == 0){
           // echo "Puzzle ".$PP." used";
            $PU[$PP] = 1;
            $PM = $PV[$PP]; // puzzle multipler  = puzzle value !This overwrites previous puzzles
        }
    }

    //complete a word
    if ($CP == 99){
        echo "<tr><td>".$w."</td>";
        if(in_array($w,$words)){
            if(in_array($w,$used_words)){

                 echo "<td>already used!</td><td></td><td></td>";
            }
            else{
                $used_words[] = $w;
                echo "<td>real word</td>";
                if($PM > 1){
                echo "<td>puzzle multiplier (x ".$PM.")</td>";
                } else { echo "<td></td>";}
                $word_score = ($l - 3) * 25 * $PM;
                echo "<td>+".($l - 3) * 25 * $PM ." points</td>";
                $score += $word_score;
                $word_score = 0;
            }
        }
        else {
            echo "<td>not scoring word</td><td></td><td></td>";
        }
        echo "</tr>";
        $w = "";
        $l = 0;
        $PM = 1;
    }
    $x += 1;

    //buy the words

    if( $CP == 666 && $buy_words == 0 ){
            $score -= 75;
            $buy_words = 1;

            echo "<tr><td></td><td>Words bought:</td><td></td><td>- 75 points</td></tr>";
    }

}



echo "<tr><td>Total Score: </td><td></td><td>".$score."</td></tr>";
$time = (strtotime($line[45])- strtotime($line[44]));
$time_mins = floor($time/60);
$time_secs = $time - ($time_mins * 60);
$time_pen = max(0,floor(($time-5400)/3));
$final_score = $score - $time_pen;
echo "<tr><td></td><td>Time</td><td>".$time_mins." min</td><td>".$time_secs."s</td></tr>";

echo "<tr><td></td><td>Penalty</td><td></td><td>".$time_pen."</td></tr>";
echo "<tr><td></td><td>Final Score</td><td></td><td>".$final_score."</td></tr>";

$results[] = [$name,$final_score];

$CPS = [];
$times = [];
$used_words = [];
$score = 0;


echo "</table>";



}
?><table id="results" class="display">
<thead>
    <tr>
        <th>Competitor</th>
        <th>Score</th>
    </tr>
</thead>
<tbody>

<?
while( $c <= count($results)){

echo "<tr><td>".$results[$c][0]."</td><td>".$results[$c][1]."</td></tr>";
$c += 1;

}

?>
</tbody>
</table>
<?
fclose($file);
?>




</body>