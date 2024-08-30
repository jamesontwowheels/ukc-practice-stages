<?PHP
session_start();
$user_ID = $_SESSION['user_ID'];
$debug = 1;
$response = [];
$debug_log = [];
$commentary = [];
$debug_log[] = "data play";

include 'db_connect.php';

ini_set("allow_url_fopen", 1);
//Get event results from DB:

$query = "select * from dbo.test_game where Player_ID = $user_ID ORDER BY Time_stamp ASC";
$result = $conn->query($query);
$i = 0;

//build punches list
$player_cps = [];
foreach ($result as $row) {
   $player_cps[$row["Player_ID"]][] = [$row["CP_ID"],$row["Time_stamp"]];
   $i += 1;
}

$debug_log[] = $i." rows";

if($debug == 1){ $debug_log[] = '19';};
$count_results = count($player_cps);
$x = 0;
//set-up the static constants (each requires it's own rule...):
    //Bulk CPS
    $words = ["THUNDER","HUNTED","HUNTED","HURDEN","HUNTER","RETUND","RUNTED","TURNED","DERTH","UNETH","DRENT","NUDER","RUNED","TENDU","TREND","TRUED","TUNED","UNDER","UNRED","URNED","TUNER","URENT","HEND","HERD","HUED","THUD","HENT","HERN","HUER","HUNT","HURT","RUTH","TEHR","THEN","THRU","DENT","DERN","DUET","DUNE","DUNT","DURE","DURN","NERD","NUDE","NURD","REND","RUDE","RUED","RUND","TEND","TUND","TURD","UNDE","URDE","RENT","RUNE","RUNT","TERN","TRUE","TUNE","TURN","DUH","EDH","ETH","HEN","HER","HET","HUE","HUN","HUT","NTH","REH","THE","DEN","DUE","DUN","END","NED","RED","RUD","TED","URD","ERN","NET","NUR","NUT","REN","RET","RUE","RUN","RUT","TEN","TUN","URE","URN","UTE"];
    $cps_letters = [1,2,3,4,5,6,7];
    $cps_bonus = [11,12];
    $all_cps = [1,2,3,4,5,6,7,11,12,20,999];
    $word = ["","N","D","R","T","H","U","E"];
    $word_value = [0,1,4,1,1,2,1,1];
    $word_length_value = [0,0,0,0,4,8,13,20];
    $word_count_bonus = [0,0,0,5,10,20,30,45,60,80,100];

    //special CPS;
    $cp_wsf = 20;
    $cp_start_finish = 999;

    $cp_names = [
        1 => "N",
        2 => "D",
        3 => "R",
        4 => "T",
        5 => "H",
        6 => "U",
        7 => "E",
        11 => "2x",
        12 => "3x",
        20 => "WSF",
        999 => "S/F"
        ];

    //results catchers
    $results_detailed = [];
    $results_summary = [];
    $results_ids= [];
    $results_names= [];
    $available_cps = [999];
    //values
    $stage_time = 60*60;

//start looping the contestants: //WE DON'T HAVE MULITPLE CONTESTANTS YET
while($x < $count_results){
    $result = $player_cps[$x]; //$results[$x];
  // don't have this data yet...
    $name = "dummy"; //update
    $surname = "data"; //update
    $finish_time = 3601 ; //update
    //check for time penalties:
        if($finish_time > $stage_time){
            $time_penalty = floor(($finish_time-$stage_time)/5);
        } else {$time_penalty = 0;}
    $x += 1;

    
if($debug == 1){ $debug_log[] = '72';};
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
    $game_state = 0;
    $game_start = 0;
    $game_end = 0;

//build and order the punches list:
 /*    while ($y < $count_cps){
        $cps[] = $result[$y]["CP_ID"];
        $times[] = $result[$y]["Time_stamp"];
        $y += 1;
    }
    array_multisort($times, $cps);
*/
    // cycle through the punch list;
    $z = 0;
    
    while ($z < $count_cps){

        // add to detailed results = $results_detailed[$id][] = [_your code_];
        // add to summary results = $results_summary[$id][] = [_your code_];

        $cp = $result[$z][0];// $cps[$z];
        $t = $result[$z][1]; //times[$z];
        $z += 1;

        if($debug == 1){ $debug_log[] = "-- cp = $cp --";};
        //pick up letter - start playing CPs 1-7
        if(in_array($cp,$cps_letters)){
            $letter = $word[$cp];
            if(in_array($cp,$used_letters)){
                //letter used in word
                $commentary[] = "Letter $letter already used";
                $results_detailed[$id][] = [$t,$cp,"letter $cp already used",0,$running_score];
            } else {
                //add to word
                $commentary[] = "Letter $letter played";
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
                $commentary[] = "bonus $cp already used";
                $results_detailed[$id][] = [$t,$cp,"bonus $cp already used","",$running_score];
            } elseif ($current_bonus > 1.5) {
                //other bonus already in play
                $used_bonuses[] = $cp;
                $commentary[] = "bonus $cp invalid, $current_bonus bonus already in use.";
                $results_detailed[$id][] = [$t,$cp,"bonus $cp invalid, $current_bonus bonus already in use.","",$running_score];
            } else {
                //award bonus
                $used_bonuses[] = $cp;
                $current_bonus = $cp - 9;
                $commentary[] = "bonus $current_bonus collected.";
                $results_detailed[$id][] = [$t,$cp,"bonus $current_bonus collected.","",$running_score];
            }
        }

        //play word
        if($cp==$cp_wsf){
            if(in_array($current_word,$words)){
                if(in_array($current_word,$used_words)){
                    
                $comment = "$current_word played, already used.";
                $commentary[] = $comment;
                $results_detailed[$id][] = [$t,$cp,$comment,"",$running_score];
                } else {
                $value = ($word_length_value[strlen($current_word)] + $current_word_value) * $current_bonus;
                $running_score += $value;
                $used_words[] = $current_word;
                $commentary[] = "$current_word successfully played!";
                $results_detailed[$id][] = [$t,$cp,"$current_word successfully played!","+ $value",$running_score];
                }
            } else {
                $commentary[] = "$current_word played, but not a known word";
                $results_detailed[$id][] = [$t,$cp,"$current_word played, but not a known word","",$running_score];
            }
            $current_word = "";
            $current_bonus = 1;
            $current_word_value = 0;
            $used_letters = [];
            $value = 0;
        }

        //start_finish
        if($cp == $cp_start_finish){
            if($game_state == 0)
            {
                $game_state = 1;
                $game_start = $t;
                $comment = "game started";
                $available_cps = $all_cps;
                $commentary[] = $comment;
                $results_detailed[$id][] = [$t,$cp,$comment,"",$running_score];
            } elseif($game_state == 1){
                $available_cps = [999];
                $game_state = 2;
                $game_end = $t;
                $comment = "game ended";
                $commentary[] = $comment;
                $results_detailed[$id][] = [$t,$cp,$comment,"",$running_score];
                 //check for time penalties:    
                $finish_time = $game_end - $game_start; //update
                if($finish_time > $stage_time){
                $time_penalty = floor(($finish_time-$stage_time)/5);
            } else {$time_penalty = 0;}
        }
        //
             else {
                $game_state = 0;
                $game_start = 0;
                $game_end = 0;
                $comment = "game reset";
                $commentary[] = $comment;
            }
        }
       

    }

    $words_found = count($used_words);
    $wf_bonus = $word_count_bonus[$words_found];
    $running_score += $wf_bonus;
    $results_detailed[$id][] = [$t,$cp,"$words_found words found, + $wf_bonus bonus","",$running_score];

    $final_score = $running_score - $time_penalty;
   $results_summary[$id][] = [$name,$surname,$time,$running_score,-$time_penalty,$final_score,$id];
}

$r = 0;
$response["available_cps"] = $available_cps;
$response["all_cps"]=$all_cps;
$response["running_score"] = $running_score;
$response["commentary"] = $commentary;
$response["current_word"] = $current_word;
$response["current_bonus"] = $current_bonus;
$response["debug_log"] = $debug_log;
$response["cp_names"] = $cp_names;
$response["game_state"] = [$game_state,$game_start,$game_end,$stage_time];

echo json_encode($response);