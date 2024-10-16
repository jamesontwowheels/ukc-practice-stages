<?PHP
session_start();
$user_ID = $_SESSION['user_ID'];
$location = $_SESSION['location'];
$debug = 1;
$response = [];
$debug_log = [];
$commentary = [];
$debug_log[] = "data play";
$user_input = $_REQUEST["user_input"];
$game = 1;

include 'db_connect.php';

//include custom php
    // e.g. include 'word_check.php';

ini_set("allow_url_fopen", 1); //this is important for fetching remote files

//Get event results from DB:

//test_game to be made into a variable
$query = "select * from dbo.test_game where Player_ID = $user_ID AND location = $location AND game = $game ORDER BY Time_stamp ASC";

if($_REQUEST["purpose"] == 2){
$query = "select * from dbo.test_game where location = $location AND game = $game ORDER BY Time_stamp ASC";

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
   $player_cps[$row["Player_ID"]][] = [$row["CP_ID"],$row["Time_stamp"],$row["puzzle_answer"]];
   $i += 1;
}
$debug_log[] = $i." rows";

if($debug == 1){ $debug_log[] = '19';};
$count_results = count($player_cps);

$x = 0;

// GAME SPECIFIC
//set-up the static constants (each requires it's own rule...):

    // e.g. $cps_letters = [1,2,3,4,5,6,7];
     //Bulk CPS
     $cps_fish = [11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29];
     $cps_seals = [31,32,33];
     $cps_oxygen = [102,202];
     //special CPS;
     $cp_trident = 333;
     $cp_start_finish = 999;
     $cp_walrus = 34;
     $cp_snow_bank = 777;
    
    $all_cps = [11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,31,32,33,34,102,202,333,777,998,999];
    $above_cps = [31,32,33,34,102,202,777,998,999];
    $below_cps = [11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,102,202,999];
    $puzzle_cps = [31,32,33];
    
    $puzzle_questions =[
        31 => "This is going to be quite a long question where we ask a few things, chuck a few numbers in there 15 x 16 7 and maybe some ??? but in the end we want you to answer yes or no. But in the meantime I think we should try and get this closer to about five hundred characters by writing and writing and writing, i'm still about 150 characters short so we might just copy and paste someting but it is actually probably quicker to keep typing because my typing speed is pretty good and i'm there now!",
        32 => "for this one i'd like a string answer of 5, so i'm hoping that we can compare at the end without tooooo much of an issue",
        33 => "here I just want something basic."
    ];

    $puzzle_answers = [
        31 => "yes",
        32 => "5",
        33 => 5
    ];
    
    $lesson_cost = [
        1 => 20,
        2 => 40
    ];

    $oxygen_amount = [
        1 => 600,
        2 => 480,
        3 => 360
    ];

    $cp_names = [
        11 => "Eel",
        12 => "Eel",
        13 => "Eel",
        14 => "Eel",
        15 => "Eel",
        16 => "Eel",
        17 => "Eel",
        18 => "Cod",
        19 => "Cod",
        20 => "Cod",
        21 => "Cod",
        22 => "Cod",
        23 => "Cod",
        24 => "Cod",
        25 => "Tuna",
        26 => "Tuna",
        27 => "Tuna",
        28 => "Tuna",
        29 => "Tuna",
        31 => "Seal",
        32 => "Seal",
        33 => "Seal",
        34 => "Walrus",
        333 => "Trident",
        777 => "Snow Bank",
        102 => "Ice Hole",
        202 => "Ice Hole",
        998 => "Finish",
        999 => "Start"
        ];

    $fish_weights = [
        "Eel" => [
            1 => 1,
            2 => 2,
            3 => 4
        ],
        "Cod" => [
            1 => 2,
            2 => 4,
            3 => 8
        ],
        "Tuna" => [
            1 => 5,
            2 => 10,
            3 => 12
        ],
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
    $fish = 5;
    $treasure = 10;
    $stage_time = 75*60;
//start looping the contestants:
while($x < $count_results){
    $player = $players[$x];
    $name = $usernames[$player];
    $player_result = $player_cps[$player]; //$results[$x];
  // don't have this data yet...
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
    $oxygen_state = [0,0];
    $oxygen = 0;
    $inventory = 0;
    $cps_seals_recruited = [];
    $seal_timers = [];
    $spear = 0;
    $bank = 0;
    $multiplier = 1;
    $fishing_level = 1;
    $available_below = $below_cps;

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
        $puzzle_answer = $player_result[$z][2];
        $z += 1;

        //EXAMPLE: pick up letter - start playing CPs 1-7
//add oxygen
if (in_array($cp,$cps_oxygen)){
    if($oxygen_state[0] == 0){
    $oxygen_state[0] = 1;
    $oxygen = $t + 75;
    $oxygen_state[1] = $oxygen;
    $comment = "Dive started";
    $available_cps = $available_below;
    } else {
        $oxygen_state = [0,0];
        $available_cps = $above_cps;
        $comment = "Dive finished";
    }
} 

//add spear
if ($cp == $cp_trident){
    $spear = 1;
    $comment = "Trident collected!";
}

//collect fish
if (in_array($cp,$cps_fish)){
    if ($t > $oxygen){
        $comment = "Oh no, out of oxygen! You've dropped everything";
        $inventory = 0;
    } elseif (!in_array($cp,$available_below)){
        $comment = "Glitch! This point $cp has already been fished";
    } else {
        $fish_name = $cp_names[$cp];
        $fish_weight = $fish_weights[$fish_name][$fishing_level];
        $comment = "$fish_name $cp caught! ".$fish_weight."kg landed";
        $inventory += $fish_weight;
        $available_below = array_diff($available_below, [$cp]);
        $available_below = array_values($available_below);
        $available_cps = $available_below;
    }            
}

//Recruit Seals:
if (in_array($cp,$cps_seals)){
    //check recruitment
    if (in_array($cp,$cps_seals_recruited)){
        // get the seal haul and reset it
        $seal_haul = floor(($t - $seal_timers[$cp])/60)/2;
        $seal_timers[$cp] = $t;
        $inventory += $seal_haul;
        $comment = "$seal_haul kg fish collected from Seal $cp";
    } else {
        if($puzzle_answer == $puzzle_answers[$cp]){
            //recruit the seal
            $comment = "puzzle solved. Seal $cp recruited";
            $cps_seals_recruited[] = $cp; //recruit the seal
            $seal_timers[$cp] = $t; //set the timer
            $puzzle_cps = array_diff($puzzle_cps, [$cp]); //remove the puzzle function
            $puzzle_cps = array_values($puzzle_cps);
        } else {
            $comment = "puzzle incorrect. -2kg fee";
            $running_score -= 2;
        }
    }            
}

//take a lesson
if ($cp == $cp_walrus){
    if($fishing_level<3){
    if($running_score >= $lesson_cost[$fishing_level]){
    $running_score -= $lesson_cost[$fishing_level];
    $comment = "Lesson $fishing_level taken! Cost of $lesson_cost[$fishing_level]";
    $fishing_level += 1;
    $available_below = $below_cps;
    } else {
        $comment = "You don't have enough fish to pay the wise walrus, no lesson taken";
    }} else {
        $comment = "You are a fully trained Level 3 ninja fishing bear";
    }
}

//snow bank
if ($cp == $cp_snow_bank){
    $running_score += $inventory;
    $comment = $inventory."kg of fish banked.";
    $inventory = 0;
}

        //start_finish
        if($cp == $cp_start_finish){
            if($game_state == 0)
            {
                $game_state = 1;
                $game_start = $t;
                $comment = "game started";
                $available_cps = $above_cps;
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

    $final_score = $running_score - $time_penalty;
       //live results
       $live_result[$name]=$final_score;
   $results_summary[$id][] = [$name,$surname,$time,$running_score,-$time_penalty,$final_score,$id];
}

//CHOOSE WHAT TO ISSUE BACK, BASED ON PORPOISE
if($_REQUEST["purpose"] !== 2){
    //GAME SPECIFIC
        //e.g. $response["upcoming_letters"] = $upcoming_letters;
$response["oxygen_state"]=$oxygen_state;
    //UNIVERSAL
$response["all_cps"]=$all_cps;
$response["available_cps"]=$available_cps;
$response["puzzle_cps"]=$puzzle_cps;
$response["puzzle_questions"]=$puzzle_questions;
$response["running_score"] = $running_score;
$response["commentary"] = $commentary;
$response["debug_log"] = $debug_log;
$response["cp_names"] = $this_cp_names;
$response["usernames"] = $usernames;
$response["game_state"] = [$game_state,$game_start,$game_end,$stage_time];
}
$response["live_scores"] = $live_result;
echo json_encode($response);