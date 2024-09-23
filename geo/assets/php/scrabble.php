<?PHP
session_start();
$user_ID = $_SESSION['user_ID'];
$location = $_SESSION['location'];
$debug = 1;
$response = [];
$debug_log = [];
$commentary = [];
$debug_log[] = "data play";

include 'db_connect.php';
include 'word_check.php';
include 'game_letters.php';
include 'valid_words.php';
include 'invalid_words.php';

ini_set("allow_url_fopen", 1);
//Get event results from DB:

foreach($result2 as $db_word){
    if($db_word['valid']){
        $valid_words_array[] = $db_word['word'];
    } else {
        $invalid_words_array[] = $db_word['word'];
    }
}

$query = "select * from dbo.test_game where Player_ID = $user_ID AND location = $location ORDER BY Time_stamp ASC";

if($_REQUEST["purpose"] == 2){
$query = "select * from dbo.test_game where location = $location ORDER BY Time_stamp ASC";

}

$result = $conn->query($query);

$usernames = [];
$query2 = "select * from dbo.users";
$stmt = $conn->prepare($query2);
    $stmt->execute();
while ($row2 = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $usernames[$row2['id']] = $row2['name'];
}

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
  
    $cps_letters = [1,2,3,4,5,6,7];
    $cps_bonus = [11,12];
    $cps_bonus_letter = [13,14];
    $all_cps = [1,2,3,4,5,6,7,11,12,13,14,20,999];
    $word = ["","A","H","M","I","E","C","N"];
    $word_value = [0,1,4,3,1,1,3,1];
    $word_length_value = [0,0,0,0,3,7,12,18];
    $word_count_bonus = [0,0,0,5,10,20,30,45,60,75,100];
    $scrabble_values = [
        'A' => 1,
        'B' => 3,
        'C' => 3,
        'D' => 2,
        'E' => 1,
        'F' => 4,
        'G' => 2,
        'H' => 4,
        'I' => 1,
        'J' => 8,
        'K' => 5,
        'L' => 1,
        'M' => 3,
        'N' => 1,
        'O' => 1,
        'P' => 3,
        'Q' => 10,
        'R' => 1,
        'S' => 1,
        'T' => 1,
        'U' => 1,
        'V' => 4,
        'W' => 4,
        'X' => 8,
        'Y' => 4,
        'Z' => 10,
        ' ' => 0,  // Blank tile has 0 points
    ];
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
        11 => "2w",
        12 => "3w",
        13 => "2l",
        14 => "3l",
        20 => "WSF",
        999 => "S/F"
        ];
    
    $this_cp_names = $cp_names;
    //results catchers
    $results_detailed = [];
    $results_summary = [];
    $results_ids= [];
    $results_names= [];
    $available_cps = [999];
    $live_result = [];
    $time_penalty = 0;
    //values
    $stage_time = 75*60;
//start looping the contestants:
while($x < $count_results){
    $player = $players[$x];
    $name = $usernames[$player];
    $player_result = $player_cps[$player]; //$results[$x];
  // don't have this data yet...
    //$name = "dummy"; //update
    $surname = "data"; //update
    $finish_time = 3601 ; //update - why is this here???
    //check for time penalties:
        if($finish_time > $stage_time){
            $time_penalty = floor(($finish_time-$stage_time)/5);
        } else {$time_penalty = 0;}
    $x += 1;

    
if($debug == 1){ $debug_log[] = '72';};
//set-up course/result variables for each contestants
    $this_cp_names = $cp_names;
    $this_word = $word;
    $letter_count = 6;
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
                //letter used in word - this is defunct code in scrabble+, but i don't want to remove it in case it breaks
                $commentary[] = "Letter $letter already used";
                $results_detailed[$id][] = [$t,$cp,"letter $cp already used",0,$running_score];
            } else {
                //add to word
                $letter_value = $scrabble_values[$letter];
                $comment = "Letter $letter played. + $letter_value points";
                $commentary[] = $comment;
                $current_word = $current_word.$this_word[$cp];
                if($letter_bonus_active){
                    $letter_value = $letter_bonus * $letter_value;
                    $comment = "Letter multiplied by $letter_bonus";
                    $commentary[] = $comment;
                    $letter_bonus_active = false;
                }
                $current_word_value += $letter_value;
                $this_cp_names[$cp] = $game_letters[$letter_count];
                $this_word[$cp] = $game_letters[$letter_count];
                $letter_count++;
                // $used_letters[] = $cp;
                $results_detailed[$id][] = [$t,$cp,"$letter collected. word = $current_word","",$running_score];
            }
        }

        //pick up word bonus 
        if(in_array($cp,$cps_bonus)){
            if(in_array($cp,$used_bonuses)){
                //bonus already played
                $commentary[] = "bonus $cp already used";
                $results_detailed[$id][] = [$t,$cp,"bonus $cp already used","",$running_score];
            } elseif ($bonus_active == true) {
                //other bonus already in play
                $used_bonuses[] = $cp;
                $comment = "bonus $cp_names[$cp] invalid, another bonus already in use.";
                $commentary[] = $comment;
                $results_detailed[$id][] = [$t,$cp,$comment,"",$running_score];
            } else {
                //award bonus
                $used_bonuses[] = $cp;
                $current_bonus = $cp - 9;
                $bonus_active = true;
                $word_bonus_active = true;
                $comment = "bonus $cp_names[$cp] collected.";
                $commentary[] = $comment;
                $results_detailed[$id][] = [$t,$cp,$comment,"",$running_score];
            }
        }

        //pick up letter bonus
        if(in_array($cp,$cps_bonus_letter)){
            if(in_array($cp,$used_bonuses)){
                //bonus already played
                $comment = "bonus $cp_names[$cp] already used";
                $commentary[] = $comment;
                $results_detailed[$id][] = [$t,$cp,$comment,"",$running_score]; //this is begggging to be rationalised
            } elseif ($bonus_active == true) {
                //other bonus already in play
                $used_bonuses[] = $cp;
                $comment =  "bonus $cp_names[$cp] invalid, another bonus is already in use.";
                $commentary[] = $comment;
                $results_detailed[$id][] = [$t,$cp,$comment,"",$running_score];
            } else {
                $used_bonuses[] = $cp;
                $comment = "bonus $cp_names[$cp] collected.";
                $letter_bonus = $cp - 11;
                $bonus_active = true;
                $letter_bonus_active = true;
                $commentary[] = $comment;
                $results_detailed[$id][] = [$t,$cp,$comment,"",$running_score];
            }
        }

        //play word
        if($cp==$cp_wsf){
            $valid = false;
            if(in_array($current_word,$valid_words_array)){
                $valid = true;
            } elseif (in_array($current_word,$invalid_words_array)){
                $valid = false;
            } else {
                $valid = isValidEnglishWord($current_word);
                $stmt = $conn->prepare("INSERT INTO words (word, valid) VALUES (:word, :valid)");
                $stmt->bindParam(':word', $current_word);
                $stmt->bindParam(':valid', $valid);
                $stmt->execute();
            }
            if($valid){
                if(in_array($current_word,$used_words)){
                    
                $comment = "$current_word played, already used.";
                $commentary[] = $comment;
                $results_detailed[$id][] = [$t,$cp,$comment,"",$running_score];
                } else {
                $value = ($word_length_value[strlen($current_word)] + $current_word_value) * $current_bonus;
                $running_score += $value;
                $used_words[] = $current_word;
                $commentary[] = "$current_word successfully played! for $value points";
                $results_detailed[$id][] = [$t,$cp,"$current_word successfully played!","+ $value",$running_score];
                }
            } else {
                $commentary[] = "$current_word played, but not a known word";
                $results_detailed[$id][] = [$t,$cp,"$current_word played, but not a known word","",$running_score];
            }
            $current_word = "";
            $current_bonus = 1;
            $letter_bonus = 1;
            $bonus_active = false;
            $letter_bonus_active = false;
            $word_bonus_active = false;
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

 

    $words_found = min(count($used_words),10);
    $wf_bonus = $word_count_bonus[$words_found];
    $running_score += $wf_bonus;
    
    $comment = "Word found bonus = $wf_bonus";
    $commentary[] = $comment;
    $results_detailed[$id][] = [$t,$cp,"$words_found words found, + $wf_bonus bonus","",$running_score];

    $final_score = $running_score - $time_penalty;
       //live results
       $live_result[$name]=$final_score;
   $results_summary[$id][] = [$name,$surname,$time,$running_score,-$time_penalty,$final_score,$id];
}

$upcoming_letters = [];
$ul = 0;
while($ul < 5){
    $ul_a = $ul + $letter_count;
    $upcoming_letters[] = $game_letters[$ul_a];
    $ul ++;
};


if($_REQUEST["purpose"] !== 2){
$response["upcoming_letters"] = $upcoming_letters;
$response["available_cps"] = $available_cps;
$response["all_cps"]=$all_cps;
$response["running_score"] = $running_score;
$response["commentary"] = $commentary;
$response["current_word"] = $current_word;
$response["current_bonus"] = $current_bonus;
$response["debug_log"] = $debug_log;
$response["cp_names"] = $this_cp_names;
$response["usernames"] = $usernames;
$response["game_state"] = [$game_state,$game_start,$game_end,$stage_time];
}
$response["live_scores"] = $live_result;
echo json_encode($response);