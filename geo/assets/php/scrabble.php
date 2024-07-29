<?PHP
$debug = 1;
echo "data play";
$user = 1;
include 'db_connect.php';

ini_set("allow_url_fopen", 1);
//Get event results from DB:

$query = "select * from dbo.test_game where Player_ID = $user ORDER BY Time_Stamp ASC";
$result = $conn->query($query);
$i = 0;
//build punches list
$player_cps = array();
foreach ($result as $row) {
   $player_cps[] = [$row["CP_ID"],$row["Time_Stamp"]];
   $i += 1;
}

echo $i." rows";

if($debug == 1){ echo '19';};
$count_data = count($data);
$yr = 0;
$results = [];
while ($yr < $count_data){
    if(in_array($data[$yr]["Player_ID"],$player_stack)){
        //do nothing
    } else {
        $results[] = $data[$yr]["Player_ID"];
    }
    $results[$data[$yr]["Player_ID"]][] = $data[$yr];
    $yr += 1;
}

if($debug == 1){ echo '32';};
$count_results = count($results);
$x = 0;
//set-up the static constants (each requires it's own rule...):
    //Bulk CPS
    $words = ["THUNDER","HUNTED","HUNTED","HURDEN","HUNTER","RETUND","RUNTED","TURNED","DERTH","UNETH","DRENT","NUDER","RUNED","TENDU","TREND","TRUED","TUNED","UNDER","UNRED","URNED","TUNER","URENT","HEND","HERD","HUED","THUD","HENT","HERN","HUER","HUNT","HURT","RUTH","TEHR","THEN","THRU","DENT","DERN","DUET","DUNE","DUNT","DURE","DURN","NERD","NUDE","NURD","REND","RUDE","RUED","RUND","TEND","TUND","TURD","UNDE","URDE","RENT","RUNE","RUNT","TERN","TRUE","TUNE","TURN","DUH","EDH","ETH","HEN","HER","HET","HUE","HUN","HUT","NTH","REH","THE","DEN","DUE","DUN","END","NED","RED","RUD","TED","URD","ERN","NET","NUR","NUT","REN","RET","RUE","RUN","RUT","TEN","TUN","URE","URN","UTE"];
    $cps_letters = [1,2,3,4,5,6,7];
    $cps_bonus = [11,12];
    $word = ["","N","D","R","T","H","U","E"];
    $word_value = [0,1,4,1,1,2,1,1];
    $word_length_value = [0,0,0,0,4,8,13,20];
    $word_count_bonus = [0,0,0,5,10,20,30,45,60,80,100];

    //special CPS;
    $cp_wsf = 20;
    $cp_start_finish = 999;

    //results catchers
    $results_detailed = [];
    $results_summary = [];
    $results_ids= [];
    $results_names= [];
    //values
    $stage_time = 60*60;

//start looping the contestants:
while($x < $count_results){
    $result = $results[$x];
  // don't have this data yet...
    $name = "dummy"; //update
    $surname = "data"; //update
    $finish_time = 3601 ; //update
    //check for time penalties:
        if($finish_time > $stage_time){
            $time_penalty = floor(($finish_time-$stage_time)/5);
        } else {$time_penalty = 0;}
    $x += 1;

    
if($debug == 1){ echo '72';};
//set-up course/result variables for each contestants
    $id = $x;
    $results_ids[] = $id;
    $results_names[$id] = [$name,$surname];
    $results_detailed[$id] = [];
    $results_summary[$id] = [];
    $count_cps = count($result);
    $y = 0;
    $cps = [];
    $times = [];
    $used_bonuses = [];
    $used_letters = [];
    $used_words = [];
    $current_word = "";
    $current_word_value = 0;
    $current_bonus = 1;
    $running_score = 0;

//build and order the punches list:
    while ($y < $count_cps){
        $cps[] = $result[$y]["CP_ID"];
        $times[] = $result[$y]["Time_stamp"];
        $y += 1;
    }
    array_multisort($times, $cps);

    // cycle through the punch list;
    $z = 0;
    
    while ($z < count($cps)){

        // add to detailed results = $results_detailed[$id][] = [_your code_];
        // add to summary results = $results_summary[$id][] = [_your code_];

        $cp = $cps[$z];
        $t = $times[$z];
        $z += 1;

        //pick up letter - start playing CPs 1-7
        if(in_array($cp,$cps_letters)){
            if(in_array($cp,$used_letters)){
                //letter used in word
                $results_detailed[$id][] = [$t,$cp,"letter $cp already used",0,$running_score];
            } else {
                //add to word
                $letter = $word[$cp];
                $current_word = $current_word.$word[$cp];
                $current_word_value += $word_value[$cp];
                $used_letters[] = $cp;
                $results_detailed[$id][] = [$t,$cp,"$letter collected. word = $current_word","",$running_score];
            }
        }

        //pick up bonus 
        if(in_array($cp,$cps_bonus)){
            if(in_array($cp,$used_bonuses)){
                //bonus already played
                $results_detailed[$id][] = [$t,$cp,"bonus $cp already used","",$running_score];
            } elseif ($current_bonus > 1.5) {
                //other bonus already in play
                $used_bonuses[] = $cp;
                $results_detailed[$id][] = [$t,$cp,"bonus $cp invalid, $current_bonus bonus already in use.","",$running_score];
            } else {
                //award bonus
                $used_bonuses[] = $cp;
                $current_bonus = $cp - 9;
                $results_detailed[$id][] = [$t,$cp,"bonus $current_bonus collected.","",$running_score];
            }
        }

        //play word
        if($cp==$cp_wsf){
            if(in_array($current_word,$words)){
                if(in_array($current_word,$used_words)){
                $results_detailed[$id][] = [$t,$cp,"$current_word played, already used.","",$running_score];
                } else {
                $value = ($word_length_value[strlen($current_word)] + $current_word_value) * $current_bonus;
                $running_score += $value;
                $used_words[] = $current_word;
                $results_detailed[$id][] = [$t,$cp,"$current_word successfully played!","+ $value",$running_score];
                }
            } else {
                $results_detailed[$id][] = [$t,$cp,"$current_word played, but not a known word","",$running_score];
            }
            $current_word = "";
            $current_bonus = 1;
            $current_word_value = 0;
            $used_letters = [];
            $value = 0;
        }

        //start_finish

        //

    }

    $words_found = count($used_words);
    $wf_bonus = $word_count_bonus[$words_found];
    $running_score += $wf_bonus;
    $results_detailed[$id][] = [$t,$cp,"$words_found words found, + $wf_bonus bonus","",$running_score];

    $final_score = $running_score - $time_penalty;
   $results_summary[$id][] = [$name,$surname,$time,$running_score,-$time_penalty,$final_score,$id];
}

$r = 0;