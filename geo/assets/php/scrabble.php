<?PHP
session_start();
$user_ID = $_SESSION['user_ID'];
$debug = 1;
$response = [];
$debug_log = [];
$commentary = [];
$debug_log[] = "data play";

include 'db_connect.php';
include 'word_check.php';
include 'game_letters.php';

ini_set("allow_url_fopen", 1);
//Get event results from DB:

$query = "select * from dbo.test_game where Player_ID = $user_ID ORDER BY Time_stamp ASC";

if($_REQUEST["purpose"] == 2){
$query = "select * from dbo.test_game ORDER BY Time_stamp ASC";
}

$result = $conn->query($query);

$i = 0;

//build punches list
$player_cps = [];
$players = [];



foreach ($result as $row) {
    if(!in_array($row["Player_ID"],$players)){
        $players[] = $row["Player_ID"];
       // $player_cps[] = $row["Player_ID"];
        $player_cps[$row["Player_ID"]] = [];
    }
   $player_cps[$row["Player_ID"]][] = [$row["CP_ID"],$row["Time_stamp"]];
   $i += 1;
}
$debug_log[] = $i." rows";

if($debug == 1){ $debug_log[] = '19';};
$count_results = count($player_cps);

$x = 0;
//set-up the static constants (each requires it's own rule...):
    //Bulk CPS
    $words = ["MACHINE","ANEMIC","CHAINE","HAEMIC","HAEMIN","HEMINA","ICEMAN","MANCHE","AMICE","AMINE","AMNIC","ANIME","CANEH","CHAIN","CHINA","CHIME","ENIAC","CHINE","HANCE","HEMIC","HEMIN","MACHE","MACHI","MANEH","MANIC","MICHE","MINAE","MINCE","NACHE","NICHE","ACHE",
    "ACME",
    "ACNE",
    "AHEM",
    "AINE",
    "AMEN",
    "AMIE",
    "AMIN",
    "ANCE",
    "CAIN",
    "CAME",
    "CAMI",
    "CANE",
    "CHAI",
    "CHAM",
    "CHEM",
    "CHIA",
    "CHIN",
    "CINE",
    "EACH",
    "EINA",
    "EMIC",
    "HAEM",
    "HAEN",
    "HAIN",
    "HAME",
    "INCH",
    "MACE",
    "MACH",
    "MAIN",
    "MANE",
    "MANI",
    "MEAN",
    "MECH",
    "MEIN",
    "MICA",
    "MICE",
    "MICH",
    "MIEN",
    "MIHA",
    "MINA",
    "MINE",
    "NACH",
    "NAME",
    "NEMA",
    "NICE","ACE",
    "ACH",
    "AHI",
    "AIM",
    "AIN",
    "AME",
    "AMI",
    "ANE",
    "ANI",
    "CAM",
    "CAN",
    "CHA",
    "CHE",
    "CHI",
    "EAN",
    "ECH",
    "HAE",
    "HAM",
    "HAN",
    "HEM",
    "HEN",
    "HIC",
    "HIE",
    "HIM",
    "HIN",
    "ICE",
    "ICH",
    "MAC",
    "MAE",
    "MAN",
    "MEH",
    "MEN",
    "MIC",
    "MNA",
    "NAE",
    "NAH",
    "NAM",
    "NIE",
    "NIM"];
    $cps_letters = [1,2,3,4,5,6,7];
    $cps_bonus = [11,12];
    $all_cps = [1,2,3,4,5,6,7,11,12,20,999];
    $word = ["","A","H","M","I","E","C","N"];
    $word_value = [0,1,4,3,1,1,3,1];
    $word_length_value = [0,0,0,0,4,8,13,20];
    $word_count_bonus = [0,0,0,5,10,20,30,45,60,80,100];

    //special CPS;
    $cp_wsf = 20;
    $cp_start_finish = 999;

    $cp_names = [
        1 => "A",
        2 => "H",
        3 => "M",
        4 => "I",
        5 => "E",
        6 => "C",
        7 => "N",
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
    $live_result = [];
    $time_penalty = 0;
    //values
    $stage_time = 60*60;
//start looping the contestants: //WE DON'T HAVE MULITPLE CONTESTANTS YET
while($x < $count_results){
    $player = $players[$x];
    $player_result = $player_cps[$player]; //$results[$x];
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
    $this_cp_names = $cp_names;
    $this_word = $word;
    $letter_count = 0;
    $id = $x;
    $results_ids[] = $id;
    $results_names[$id] = [$name,$surname];
    $results_detailed[$id] = [];
    $results_summary[$id] = [];
    $count_cps = count($player_result);
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

        $cp = $player_result[$z][0];// $cps[$z];
        $t = $player_result[$z][1]; //times[$z];
        $z += 1;

        if($debug == 1){ $debug_log[] = "-- cp = $cp --";};
        //pick up letter - start playing CPs 1-7
        if(in_array($cp,$cps_letters)){
            $letter = $this_word[$cp];
            if(in_array($cp,$used_letters)){
                //letter used in word
                $commentary[] = "Letter $letter already used";
                $results_detailed[$id][] = [$t,$cp,"letter $cp already used",0,$running_score];
            } else {
                //add to word
                $commentary[] = "Letter $letter played";
                $current_word = $current_word.$this_word[$cp];
                $current_word_value += $word_value[$cp];
                $this_cp_names[$cp] = $game_letters[$letter_count];
                $this_word[$cp] = $game_letters[$letter_count];
                $letter_count += 1;
                // $used_letters[] = $cp;
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
            if(isValidEnglishWord($current_word)){
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
       //live results
       $live_result[$x]=$final_score;
   $results_summary[$id][] = [$name,$surname,$time,$running_score,-$time_penalty,$final_score,$id];
}

if($_REQUEST["purpose"] !== 2){
$response["available_cps"] = $available_cps;
$response["all_cps"]=$all_cps;
$response["running_score"] = $running_score;
$response["commentary"] = $commentary;
$response["current_word"] = $current_word;
$response["current_bonus"] = $current_bonus;
$response["debug_log"] = $debug_log;
$response["cp_names"] = $this_cp_names;
$response["game_state"] = [$game_state,$game_start,$game_end,$stage_time];
}
$response["live_scores"] = $live_result;
echo json_encode($response);