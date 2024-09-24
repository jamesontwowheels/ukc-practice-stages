<?php

/*
CP 1 = T
CP 2 = W
CP 3 = I
CP 4 = N
CP 5 = k
CP 6 = L
CP 7 = E
CP 8 = Full stop
CP 9 = S/F
CP 10 = PP1
CP 11 = PP2
...
*/

$words = ["TWINKLE","WINKLE","WELKIN","TINKLE","WINTLE","WELKT","TWINK","LIKEN","INKLE","KNELT","TWINE","INLET","ENLIT","ELINT","INTEL","LENTI","WELK","TILE","WILT","WEIL","WIEL","WILE","WENT","LITE","TINE","NEWT","TEIL","TEIN","LENT","LINT","LINE","TWIN"];

$file = fopen('results.csv', 'r');
while (($line = fgetcsv($file)) !== FALSE) {
  
$w = "";            //word
$l = 0;             //length of word
$SF = 0;            //start finish
$PM = 1;            //puzzle multiplier
$PU = [0,0,0,0];    //puzzles used
$PV = [2,2,2,2];    //puzzle value
$word = ["","I","E","K","T","W","L","N"];
$x = 46;
$y = 46;
$CPS = [];

echo "<table>";

while ($y <= count ($line)){
    $z = $y +1;
    $CPS[] = $line[$y];
    $times[] = strtotime($line[$z]);
    //build our arrays
    $y += 2;

}
$ar1 = array(10, 100, 100, 0);
$ar2 = array(1, 3, 2, 4);
array_multisort($times, $CPS);


while( $x <= count($line)){

    $CP = $line[$x];

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

            echo "<td>real word</td>";
            if($PM > 1){
               echo "<td>puzzle multiplier (x ".$PM.")</td>";
            } else { echo "<td></td>";}
            echo "<td>score = ".$l." function</td>";
        }
        else {
            echo "<td>not real word</td><td></td><td></td>";
        }
        echo "</tr>";
        $w = "";
        $l = 0;
        $PM = 1;
    }
    $x += 2;

    //start-finish

    if( $CP == 9 ){
        if( $SF == 0){
            echo "start<br>";
            $SF = 1;
        }
        else {
            echo "finish<br>";
        }
    }

}

// echo "<BR>new line<br>";

echo "</table>";
}

fclose($file);
?>