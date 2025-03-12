<?PHP
session_start();

$user_ID = $_SESSION['user_ID'];
$location = $_SESSION['location'];
$game = $_SESSION['game'];
$debug = 1;
$teams_active = true;
$response = [];
$debug_log = [];
$debug_log[] = "data play";
$user_input = $_REQUEST["user_input"];
$incoming_cp = $cp;
include 'db_connect.php';

//include custom php
    // e.g. include 'word_check.php';

ini_set("allow_url_fopen", 1); //this is important for fetching remote files

//Get event results from DB:

//test_game to be made into a variable
$query = "select * from dbo.test_game where location = $location AND game = $game ORDER BY Time_stamp ASC";

if($_REQUEST["purpose"] == 2){ //irrelevant as we need everything in a teams scenario
$query = "select * from dbo.test_game where location = $location AND game = $game ORDER BY Time_stamp ASC";
}

$result = $conn->query($query);

$query = "select * from dbo.test_game where game = :game and location = :location";
    $stmt = $conn->prepare($query);
    // Bind values to the placeholders
    $stmt->bindValue(':game', $game, PDO::PARAM_INT);
    $stmt->bindValue(':location', $location, PDO::PARAM_INT);
    $stmt->execute();

$usernames = [];
$query2 = "select * from dbo.users";

//don't strictly always need all users. but haven't got a viable filter on this yet. This will come from the pre-game page
$stmt2 = $conn->prepare($query2);
    $stmt2->execute();
while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    $usernames[$row2['id']] = $row2['name'];
}

if($teams_active){
    $debug_log[]  = "44. teams active";
    //get all the teams
    $query3 = "select * from dbo.teams where game = :game and location = :location";
    $stmt3 = $conn->prepare($query3);
    // Bind values to the placeholders
    $stmt3->bindValue(':game', $game, PDO::PARAM_INT);
    $stmt3->bindValue(':location', $location, PDO::PARAM_INT);
    $stmt3->execute();
    while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
        $teams[$row3['UID']] = [
            "name" => $row3['name'],
            "members" => [],
            "cps" => [],
            "score" => 0,
            "params" => [
                "snakes" => [],
                "fruit" => 0,
                "fruit_box" => [],
                "commentary" => [],
                "location" => 0
            ]
        ];
    $debug_log[]  = "58. count teams";
    }
    $debug_log[] = $teams;

    //get all the team_members
    $query4 = "select * from dbo.team_members where game = :game and location = :location";
    $stmt4 = $conn->prepare($query4);
    // Bind values to the placeholders
    $stmt4->bindValue(':game', $game, PDO::PARAM_INT);
    $stmt4->bindValue(':location', $location, PDO::PARAM_INT);
    $stmt4->execute();
    $teamed_players = [];
    $player_details = [];
    while ($row4 = $stmt4->fetch(PDO::FETCH_ASSOC)) {
       $teams[$row4["team"]]["members"][] = $row4["player_ID"];
       $teamed_players[] = $row4["player_ID"];
       if($row4["player_ID"] == $user_ID){
        $this_team = $row4["team"];
       }
       //set-up the player
       $player_details[$row4["player_ID"]] = [ 
            "team" => $row4["team"],
            "name" => $usernames[$row4["player_ID"]],
            "params" => [ "last_cp" => 8]
        ];
    $debug_log[]  = "73. count team_members";
    $debug_log['player details'] = $player_details;
    }
}

$i = 0;

//build punches list
$player_cps = [];
$all_punches = [];
$players = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if(!in_array($row["Player_ID"],$players)){
        $players[$row["Player_ID"]] = []; //this means that we're only initiating players once they have hit their first checkpoint, does this matter?
    }
  $all_punches[] = [$row["CP_ID"],$row["Time_stamp"],$row["puzzle_answer"],$row["Player_ID"],$player_details[$row["Player_ID"]]["team"],$row["cp_option"]]; //this has all punches now.
   $i += 1;
}
$debug_log[] = $all_punches;

//build the teams

$x = 0;

// GAME SPECIFIC
//set-up the static constants (each requires it's own rule...):

    // e.g. $cps_letters = [1,2,3,4,5,6,7];
     //Bulk CPS

     //special CPS;
     $cp_start_finish = [998,999];

    $cp_bible = [
        1 => [
            "cp" => 1,
            "name" => "A",
            "type" => "dice",
            "score" => [1,2,3],
            "puzzle" => false,
            "puzzle_q" => "",
            "puzzle_a" => "",
            "message" => "Checkpoint A",
            "options" => [
                1 => "Move"
            ],
            "available" => true
        ],
        2 => [
            
            "cp" => 2,
            "name" => "B",
            "type" => "dice",
            "score" => [29,2,3],
            "puzzle" => false,
            "puzzle_q" => "",
            "puzzle_a" => "",
            "message" => "Checkpoint A",
            "options" => [
                1 => "Move"
            ],
            "available" => false
        ],
        11 => [
            
            "cp" => 11,
            "name" => "Apple",
            "type" => "fruit",
            "score" => [29,2,3],
            "puzzle" => true,
            "puzzle_q" => "What is up?",
            "puzzle_a" => "sky",
            "message" => "Checkpoint A",
            "options" => [
                1 => "solve"
            ],
            "available" => false
        ],
        999 => [
            
            "cp" => 999,
            "name" => "s/f",
            "type" => "start_finish",
            "score" => [1,2,3],
            "puzzle" => false,
            "puzzle_q" => "x",
            "puzzle_a" => "x",
            "message" => "Checkpoint A",
            "options" => [
                1 => "Move"
            ],
            "available" => true
        ]
         //etc
    ];

    $special_squares = [
        10 => [
            "type" => "snake",
            "endpoint" => 2    
        ],
        30 => [
            "type" => "ladder",
            "endpoint" => 45    
        ],
        55 => [
            "type" => "snake",
            "endpoint" => 29    
        ]
    ];


   // $this_cp_names = $cp_names; //required if cpnames are going to change.

    //results catchers (don't change this, it's solid)
    $results_detailed = [];
    $results_summary = [];
    $results_ids= [];
    $results_names= [];
    $available_cps = []; //which CPs are immediately available?
    $live_result = [];

    //values
    $hand_limit = 2;
    $stage_time = 75*60;
    $alert = 0;

//TEAM SPECIFIC catchers (customise the catchers here)

    if($user_ID == 29){
        $cp_bible[999]["available"] = true;
    }

    $pl_finishers=  [];

//PLAYER SPECIFIC customise $players here

    foreach($players as $player){
        $player["hand"] = 0;
        $player["history"] = [];
    }
    // for each player
    // history
    // inventory


//start looping the contestants:
foreach($teams as $team_UID => $team){

    if($_REQUEST["purpose"] != 45){
        if($team_UID != $this_team){
            $debug_log[] = "skipping $team_UID";
            continue; //skipping teams that aren't the active one
        }
    }

    $debug_log[] = "playing with $team_UID";
        //while($x < $count_results){
    /// not needed $team_UID = key($team);
    $name = $team["name"];
    $team_result = $team["cps"]; //$results[$x];
  // don't have this data yet...
    $surname = "data"; //update
    $finish_time = 0 ; //update - why is this here???
    $team_player_count = 0;
    $team_finish_count = 0;
    $x += 1;

    
if($debug == 1){ $debug_log[] = '72';};
//GAME SPECIFIC set-up course/result variables for each contestants
   // $this_cp_names = $cp_names;

    foreach($team["members"] as $team_member){
        // $available_cps[$team_member] = [999]; CPs are per team in this game
        $team_player_count += 1;
    }
    
//GENERIC player specific starting values
    $id = $x;
    $results_ids[] = $id;
    $results_names[$id] = [$name,$surname];
    $results_detailed[$id] = [];
    $results_summary[$id] = [];
    //$commentary = [];
    $count_cps = count($all_punches);
    $y = 0;
    $running_score = 0;
    $game_state = 0;
    $game_start = 0;
    $game_end = 0;
    $game_time = 0;
    $time_penalty = 0;


    // cycle through the punch list;
    $z = 0;
    
    while ($z < $count_cps){
 
        // add to detailed results = $results_detailed[$id][] = [_your code_];
        // add to summary results = $results_summary[$id][] = [_your code_];

        $cp_number = intval($all_punches[$z][0]);
        $cp = $cp_bible[$cp_number];// $cps[$z];
        $t = $all_punches[$z][1]; //times[$z];
        $puzzle_answer = strtolower($all_punches[$z][2]);
        $pl = intval($all_punches[$z][3]);
        $tm = intval($all_punches[$z][4]);
        $purp = $all_punches[$z][5];
        $debug_log['297'] = $all_punches[$z];
        $z += 1;
        $puzzle_response = 0;
        $alert = 0;
        $game_time = $t - $game_start;
        $timezone = floor($game_time/1800);

        //Dice Rolls
        if($cp["type"] == "dice") {
            if($cp_number == $player_details[$pl]["params"]["last_cp"]){
                $comment = "You can't visit the same checkpoint twice in a row";
            } else{
                $debug_log['cp_number'] = $cp_number;
                $debug_log['team'] = $teams[$tm];
                $player_details[$pl]["params"]["last_cp"] = $cp_number;
                $comment = "you moved a few squares";
                $new_location = min(100, $teams[$tm]['params']['location'] + $cp["score"][$timezone]);
                if($new_location == 100) {$new_location = 0;}
                if(array_key_exists($new_location,$special_squares)){
                    $debug_log["eaten"] = true;
                    $this_special = $special_squares[$new_location];
                    if($this_special['type'] == "snake"){
                        if(in_array($new_location,$teams[$tm]["params"]["snakes"])){
                            $comment = "snake already captured";
                        } elseif ($teams[$tm]["params"]["fruit"]>0){
                            $teams[$tm]["params"]["fruit"] -= 1;
                            $teams[$tm]["params"]["snakes"][] = $new_location;
                            $teams[$tm]["score"] += 20;
                            $comment = "snake captured";
                        } else {
                            $new_location = $this_special['endpoint'];
                            $comment = "you were eaten by a snake!";
                        }
                    } else {
                        $new_location = $this_special['endpoint'];
                        $comment = "you climbed a ladder";
                    }
                } 
                $teams[$tm]['params']['location'] = $new_location;
            }
        }

        //Fruit stops

        if($cp['type'] == 'fruit'){
            if(in_array($cp_number,$teams[$tm]["params"]['fruit_box'])){
                $comment = "Fruit already collected";
            } else {
                if($puzzle_answer == $cp["puzzle_a"]){
                    $teams[$tm]["params"]["fruit_box"][] = $cp_number;
                    $teams[$tm]["params"]["fruit"] += 1;
                    $comment = "puzzle solved, fruit collected";
                    if($tm == $this_team){
                        $cp_bible[$cp_number]["message"] = "You have collected this fruit already";
                        $cp_bible[$cp_number]["options"] = [];
                    }
                }
                else {
                    $comment = "incorrect answer";
                }
            }
        }

        //start_finish
        if($cp['type'] == "start_finish"){
            if($cp_number == 999){
            if($game_state == 0)
            {
                $game_state = 1;
                $game_start = $t;
                $comment = "game started";
                if($tm == $this_team){
                    foreach ($cp_bible as &$checkpoint) {
                        $checkpoint["available"] = true;
                    }
                    unset($checkpoint);
                    $cp_bible[999]["available"] = false;
                }
            } 
            elseif ($game_state == 2) {
               $game_state = 0;
               $game_start = 0;
               $game_end = 0;
               $comment = "game reset";
           } }
            elseif
            ($cp_number == 998){
                if(in_array($pl,$pl_finishers)){
                    $comment = "already finished";
                } else {
                    $pl_finishers[] = $pl;
                    $comment = "finished";
                    if($tm == $this_team){
                        foreach ($cp_bible as &$checkpoint) {
                            $checkpoint["available"] = false;
                        }
                        unset($checkpoint);
                        $cp_bible[999]["available"] = true;
                    }
                }
            }
        }
        //
        

        //ONCE THE CP ACTION HAS BEEN TAKEN:
        $teams[$tm]["params"]["commentary"][] = "Player ".$pl." - ".$comment;
        $results_detailed[$id][] = [$t,$cp_number,$comment,"",$running_score];
    }

    //ONCE WE HAVE CYCLED THROUGH THE CPs..

    $final_score = $running_score - $time_penalty;
    $time = $game_time;
       //live results
        //$live_result[$name]=$final_score;
        //$results_summary[$id][] = [$name,$surname,$time,$running_score,-$time_penalty,$final_score,$id];
   
}
$final_results = [];

foreach ($teams as $team) {
    if (isset($team['name']) && isset($team['score'])) {
        $final_results[$team['name']] = $team['score'] + $team['params']["location"];
    }
}

$debug_log[] = $final_results;


//CHOOSE WHAT TO ISSUE BACK, BASED ON PORPOISE

if($_REQUEST["purpose"] !== 2){
    //GAME SPECIFIC
    //UNIVERSAL
$response["cp_bible"]= $cp_bible; //available_cps[$user_ID]; THIS NEEDS TO BE UPDATED!!!
//don't send back a puzzle response if nothing has been submitted.
if($incoming_cp > 0) {
$response["puzzle_response"]=$puzzle_response;
$response["comment"] = $comment;}
$response["running_score"] = $running_score;
$response["alert"] = $alert;
$response["this_team"] = $this_team;
$response["teams"] = $teams;
$response["usernames"] = $usernames;
$response["game_state"] = [$game_state,$game_start,$game_end,$stage_time];
$response["inventory"] = [
    "Position on Board" => $teams[$this_team]["params"]["location"],
    "Fruit in basket" => $teams[$this_team]["params"]["fruit"]
];
}
$response["live_scores"] = $final_results;
$response["commentary"] = $teams[$this_team]["params"]["commentary"];
$response["debug_log"] = $debug_log;
echo json_encode($response);
