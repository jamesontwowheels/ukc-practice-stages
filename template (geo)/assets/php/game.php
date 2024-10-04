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

//include custom php
    // e.g. include 'word_check.php';

ini_set("allow_url_fopen", 1); //this is important for fetching remote files

//Get event results from DB:

//test_game to be made into a variable
$query = "select * from dbo.test_game where Player_ID = $user_ID AND location = $location ORDER BY Time_stamp ASC";

if($_REQUEST["purpose"] == 2){
$query = "select * from dbo.test_game where location = $location ORDER BY Time_stamp ASC";

}

$result = $conn->query($query);

$usernames = [];
$query2 = "select * from dbo.users";

//don't strictly always need all users. but haven't got a viable filter on this yet.
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

// GAME SPECIFIC
//set-up the static constants (each requires it's own rule...):

    // e.g. $cps_letters = [1,2,3,4,5,6,7];
    
    //special CPS;
    
    $all_cps = [1,2,3,4,5,6,7,11,12,13,14,20,999];
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
    
    $this_cp_names = $cp_names; //required if cpnames are going to change.

    //results catchers
    $results_detailed = [];
    $results_summary = [];
    $results_ids= [];
    $results_names= [];
    $available_cps = [999]; //which CPs are immediately available?
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
//GAME SPECIFIC set-up course/result variables for each contestants
    $this_cp_names = $cp_names;
    $this_word = $word;
    $letter_count = 6;

//GENERIC
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


    // cycle through the punch list;
    $z = 0;
    
    while ($z < $count_cps){
 
        // add to detailed results = $results_detailed[$id][] = [_your code_];
        // add to summary results = $results_summary[$id][] = [_your code_];

        $cp = $player_result[$z][0];// $cps[$z];
        $t = $player_result[$z][1]; //times[$z];
        $z += 1;

        if($debug == 1){ $debug_log[] = "-- cp = $cp --";};
        //EXAMPLE: pick up letter - start playing CPs 1-7
        if(in_array($cp,$cps_letters)){
            $letter = $this_word[$cp];
            if(in_array($cp,$used_letters)){
                //letter used in word - this is defunct code in scrabble+, but i don't want to remove it in case it breaks
                $comment = "Letter $letter already used";
            } else {
                //add to word
                $letter_value = $scrabble_values[$letter];
                $comment = "Letter $letter played. + $letter_value points";
                $current_word = $current_word.$this_word[$cp];
                if($letter_bonus_active){
                    $letter_value = $letter_bonus * $letter_value;
                    $comment = "Letter multiplied by $letter_bonus";
                    $letter_bonus_active = false;
                }
                $current_word_value += $letter_value;
                $this_cp_names[$cp] = $game_letters[$letter_count];
                $this_word[$cp] = $game_letters[$letter_count];
                $letter_count++;
                // $used_letters[] = $cp;
               }
        }

        //start_finish
        if($cp == $cp_start_finish){
            if($game_state == 0)
            {
                $game_state = 1;
                $game_start = $t;
                $comment = "game started";
                $available_cps = $all_cps;
            } elseif($game_state == 1){
                $available_cps = [999];
                $game_state = 2;
                $game_end = $t;
                $comment = "game ended";
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

        //ONCE THE CP ACTION HAS BEEN TAKEN:
        $commentary[] = $comment;
        $results_detailed[$id][] = [$t,$cp,$comment,"",$running_score];
    }

    //ONCE WE HAVE CYCLED THROUGH THE CPs..


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

//CHOOSE WHAT TO ISSUE BACK, BASED ON PORPOISE
if($_REQUEST["purpose"] !== 2){
    //GAME SPECIFIC
        //e.g. $response["upcoming_letters"] = $upcoming_letters;
    //UNIVERSAL
$response["all_cps"]=$all_cps;
$response["running_score"] = $running_score;
$response["commentary"] = $commentary;
$response["debug_log"] = $debug_log;
$response["cp_names"] = $this_cp_names;
$response["usernames"] = $usernames;
$response["game_state"] = [$game_state,$game_start,$game_end,$stage_time];
}
$response["live_scores"] = $live_result;
echo json_encode($response);