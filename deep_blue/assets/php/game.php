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
     //Bulk CPS
     $cps_treasure = [11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29];
     $cps_fish = [31,32,33];
     $cps_oxygen = [102,202];
     //special CPS;
     $cp_trident = 333;
     $cp_start_finish = 999;
     $cp_poseidons_gamble = 34;
     $cp_dive_boat = 777;
    
    $all_cps = [11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,31,32,33,34,102,202,333,777,998,999];
    
    $cp_names = [
        11 => "Nemo",
        12 => "Nemo",
        13 => "Nemo",
        14 => "Nemo",
        15 => "Nemo",
        16 => "Nemo",
        17 => "Nemo",
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
        31 => "Whale",
        32 => "Whale",
        33 => "Whale",
        34 => "Gamble",
        333 => "Trident",
        777 => "Boat",
        102 => "Air",
        202 => "Air",
        998 => "Finish",
        999 => "Start"
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
    $oxygen = 0;
    $inventory = [];
    $spear = 0;
    $bank = [];
    $multiplier = 1;

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

        //EXAMPLE: pick up letter - start playing CPs 1-7
//add oxygen
if (in_array($cp,$cps_oxygen)){
    $oxygen = $t + 600;
    $comment = "Oxygen picked up";
} 

//add spear
if ($cp == $cp_trident){
    $spear = 1;
    $comment = "Trident collected!";
}

//collect treasure
if (in_array($cp,$cps_treasure)){
    if ($t > $oxygen){
        $comment = "Oh no, out of oxygen! You've dropped everything";
        $inventory = [];
    } elseif (in_array($cp,$inventory)){
        $comment = "Treasure already held";
    }elseif (in_array($cp,$bank)){
        $comment = "Treasure already in the bank";
    } else {
        $comment = "Treasure ".$cp." picked-up";
        $inventory[] = $cp;
    }            
}

//collect fish:
if (in_array($cp,$cps_fish)){
    //check spear:
    if($spear == 0){
        $comment = "You tried to pick-up fish $cp with no trident";
    } elseif (in_array($cp,$inventory)){
        $comment = "Fish already caught this trip";
    } elseif ($t > $oxygen){
        $comment = "Oh no, out of oxygen! You've dropped everything";
        $inventory = [];
    } else {
        $comment = "Fish $cp speared!";
        $inventory[] = $cp;
    }            
}

//take gamble
if ($cp == $cp_poseidons_gamble){
    $threshold = 80 * $multiplier;
    if($running_score >= $threshold){
    $running_score -= $threshold;
    $comment = "Gamble taken!";
    $multiplier += 1;
    $bank = [];
    } else {
        $comment = "You don't have enough treasure to pay Poseidon, no gamble taken";
    }
}

//dive boat
if ($cp == $cp_dive_boat){
    $i = 0;
    while($i < count($inventory)){
        $item = $inventory[$i];
        if (in_array($item,$cps_fish)){    
            $value = $fish * $multiplier;
            $running_score += $value;
            $comment = "Fish $item landed!";
            } elseif (in_array($item,$cps_treasure)){
            $value = $treasure * $multiplier;
            $running_score += $value;
            $comment = "Treasure ".$item." stashed!";
            $bank[] = $item;
        }
        $i += 1;
    }
    // empty inventory:
    $inventory = [];
    $spear = 0;
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
$response["available_cps"]=$available_cps;
$response["running_score"] = $running_score;
$response["commentary"] = $commentary;
$response["debug_log"] = $debug_log;
$response["cp_names"] = $this_cp_names;
$response["usernames"] = $usernames;
$response["game_state"] = [$game_state,$game_start,$game_end,$stage_time];
}
$response["live_scores"] = $live_result;
echo json_encode($response);