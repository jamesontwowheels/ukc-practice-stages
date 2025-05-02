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

//game specifics (to include in teams)

include 'cp_bible.php';
include 'puzzle_bible.php';

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
            "params" => [
                "game" => [
                    "game_start" => 0,
                    "game_state" => 0,
                    "game_end" => 0
                ],
                "score" => 0,
                "commentary" => [],
                "cp_bible" => $cp_bible,
            ]
        ];
    }

    //get all the team_members
    $query4 = "select * from dbo.team_members where game = :game and location = :location";
    $stmt4 = $conn->prepare($query4);
    // Bind values to the placeholders
    $stmt4->bindValue(':game', $game, PDO::PARAM_INT);
    $stmt4->bindValue(':location', $location, PDO::PARAM_INT);
    $stmt4->execute();
    $teamed_players = [];
    $players = [];
    while ($row4 = $stmt4->fetch(PDO::FETCH_ASSOC)) {
       $teams[$row4["team"]]["members"][] = $row4["player_ID"];
       $teamed_players[] = $row4["player_ID"];
       if($row4["player_ID"] == $user_ID){
        $this_team = $row4["team"];
       }
       //set-up the player
       $players[$row4["player_ID"]] = [ 
            "team" => $row4["team"],
            "name" => $usernames[$row4["player_ID"]],
            "params" => [ "used_cps" => []],
            "hand" => 0,
            "history" => [],
            "inventory" => [
                "Kids" => 2,
                "Cats" => 1,
                "Years" => 10
            ]
        ];
    $debug_log['player details'] = $players;
    }
}

$i = 0;

//build punches list
$player_cps = [];
$all_punches = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $all_punches[] = [$row["CP_ID"],$row["Time_stamp"],$row["puzzle_answer"],$row["Player_ID"],$players[$row["Player_ID"]]["team"],$row["cp_option"]]; //this has all punches now.
   $i += 1;
}
$debug_log[] = $all_punches;

//build the teams

$x = 0;

// GAME SPECIFIC
//set-up the static constants (each requires it's own rule...):

     //special CPS;
     $cp_start_finish = [998,999];


     //results catchers (don't change this, it's solid)
    $results_detailed = [];
    $results_summary = [];
    $results_ids= [];
    $results_names= [];
    $available_cps = []; //which CPs are immediately available?
    $live_result = [];

    //values
    $hand_limit = 2;
    $stage_time = 150*60;
    $alert = 0;

//TEAM SPECIFIC catchers (customise the catchers here)

    if($user_ID == 29){
        $cp_bible[999]["available"] = true;
    }

    $pl_finishers=  [];

//PLAYER SPECIFIC customise $players here

    foreach($players as $player){
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
    //$game_state = 0;
    //$game_start = 0;
    //$game_end = 0;
    $game_time = 0;
    $time_penalty = 0;
    $current_timezone = 0;


    // cycle through the punch list;
    $z = 0;
    
    while ($z < $count_cps){
 
        // add to detailed results = $results_detailed[$id][] = [_your code_];
        // add to summary results = $results_summary[$id][] = [_your code_];

        $cp_number = intval($all_punches[$z][0]);
        $pl = intval($all_punches[$z][3]);
        $tm = intval($all_punches[$z][4]);
        $cp_option = intval($all_punches[$z][5]);
        $cp = $teams[$tm]["params"]["cp_bible"][$cp_number];// $cps[$z];
        $t = $all_punches[$z][1]; //times[$z];
        $puzzle_answer = strtolower($all_punches[$z][2]);
        $purp = $all_punches[$z][5];
        $debug_log['297'] = $all_punches[$z];
        $z += 1;
        $puzzle_response = 0;
        $alert = 0;
        $game_time = $t - $teams[$tm]["params"]["game"]["game_start"];

        if($game_time > $stage_time && $cp_number != 999 && false ){
            foreach ($cp_bible as $key => $cp) {
                    $teams[$tm]["params"]["cp_bible"][$key]['available'] = false;
                    }
                    $comment = "The game has ended.";
                }
                else {

        //Treasure points
        if($cp['type'] == "target"){
            $next_cp_number = $cp_number + 1;
            $previous_cp_number = $cp_number - 1;
            if($cp_number > 1){
            $teams[$tm]["params"]["cp_bible"][$previous_cp_number]['available'] = false;
            }
            $teams[$tm]["params"]["cp_bible"][$next_cp_number]['available'] = true;
            $teams[$tm]["params"]["cp_bible"][$cp_number]['options'] = [];
            $comment = "Memory collected";
            if($cp_number == 3 || $cp_number == 4){
                
            $teams[$tm]["params"]["score"] += 0.5;
            } else {
            $teams[$tm]["params"]["score"] += 1;
            }
            
            if($cp_number == 11){
                $teams[$tm]["params"]["cp_bible"][998]['available'] = true;
                $teams[$tm]["params"]["cp_bible"][$cp_number]["message"] = "Well done on completing the trip down memory lane. <h3>Happy 10th Anniversary</h3><br><br><img class='puzzle_pic' src='assets/img/w11.jpeg'>";
            }
    }

        //start_finish
        if($cp['type'] == "start_finish"){
            if($cp_number == 999){
            if($teams[$tm]["params"]["game"]["game_state"] == 0)
            {
                
                //require 'start_game.php';
                $teams[$tm]["params"]["game"]["game_state"] = 1;
                $teams[$tm]["params"]["game"]["game_start"] = $t;
                $debug_log[]  = "game state =" . $teams[$tm]["params"]["game"]["game_state"];
                $teams[$tm]["params"]["cp_bible"][999]["available"] = false;
                $teams[$tm]["params"]["cp_bible"][1]["available"] = true;
                $teams[$tm]["params"]["cp_bible"][999]["options"] = [];
                $teams[$tm]["params"]["cp_bible"][999]["message"] = "";
                    $comment = "Have fun!";
                
            } 
            elseif ($teams[$tm]["params"]["game"]["game_state"] == 4) { //taking this out of operation
               //$game_state = 0;
               //$game_start = 0;
               //$game_end = 0;
               $teams[$tm]["params"]["game"]["game_state"] = 0;
               $teams[$tm]["params"]["game"]["game_start"] = 0;
               $teams[$tm]["params"]["game"]["game_end"] = 0;
               $comment = "game reset";
           } }
            elseif
            ($cp_number == 998){
                if(in_array($pl,$pl_finishers)){
                    $comment = "already finished";
                } elseif ($game_time >= $stage_time ) {
                    $comment = "too late to finish";
                } else {
                    $pl_finishers[] = $pl;
                    $finish_bonus = 50/(count($teams[$tm]["members"]));
                        $teams[$tm]["params"]["score"] += $finish_bonus;
                        unset($checkpoint);
                        $comment = "Finished. Bonus: $finish_bonus";
                    if($pl == $user_ID){
                        foreach ($teams[$tm]["params"]["cp_bible"] as &$checkpoint) {
                            $checkpoint["available"] = false;
                        } 
                        unset($checkpoint);
                    }
                }
            }
        }
        //
    }

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
   

$final_results = [];

foreach ($teams as $team) {
    if (isset($team['name']) && isset($team['score'])) {
        $final_results[$team['name']] = $team['score'] + $team['params']["score"];
    }
}

$debug_log[] = $final_results;
$debug_log[] = $teams;

//CHOOSE WHAT TO ISSUE BACK, BASED ON PORPOISE

if($_REQUEST["purpose"] !== 2){
    //GAME SPECIFIC
    //UNIVERSAL
$response["cp_bible"]= $teams[$this_team]["params"]["cp_bible"]; //available_cps[$user_ID]; THIS NEEDS TO BE UPDATED!!!
//don't send back a puzzle response if nothing has been submitted.
if($incoming_cp > 0) {
$response["puzzle_response"]=$puzzle_response;
$response["comment"] = $comment;}
$response["running_score"] = $running_score;
$response["alert"] = $alert;
$response["this_team"] = $this_team;
$response["usernames"] = $usernames;
$response["game_state"] = [$teams[$this_team]["params"]["game"]["game_state"],$teams[$this_team]["params"]["game"]["game_start"],$teams[$this_team]["params"]["game"]["game_end"],$stage_time];
$response["inventory"] = $players[$user_ID]["inventory"];
}
$response["teams"] = $teams;
$response["live_scores"] = $final_results;
$response["commentary"] = $teams[$this_team]["params"]["commentary"];
$response["debug_log"] = $debug_log;
$response["db_response"] = $db_response;
echo json_encode($response);