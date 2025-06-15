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

include 'level_bible.php';
include 'puzzle_bible.php';
include 'cp_bible.php';

include 'db_connect.php';


ini_set("allow_url_fopen", 1); //this is important for fetching remote files

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
                "game" => [
                    "game_start" => 0,
                    "game_state" => 0,
                    "game_end" => 0
                ],
                "score" => 0,
                "commentary" => [],
                "level" => 0,
                "cp_bible" => $cp_bible,
                "nim" => [  41 => [],
                            42 => [1,2,3,4,5],
                            43 => [],
                            "gold" => 25],
                "pairs" => ["word" => "","time" => 0],
                "pairs_codes" => ["turntable","laptop","notebook"],
                "pairs_codes_used" => [],
                "pairs_found" => 0
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
            "params" => [ "last_pair" => 0],
            "history" => [],
            "inventory" => [ "level" => 1]
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
    $stage_time = 400000*60;
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

        if($game_time > $stage_time && $cp_number != 999 ){
            foreach ($cp_bible as $key => $cp) {
                    $teams[$tm]["params"]["cp_bible"][$key]['available'] = false;
                    }
                    $comment = "The game has ended.";
                }
                else {
        
        //ladder
        
        if($cp['type'] == 'ladder'){
            //go up the ladder (player location)
            $players[$pl]["inventory"]["level"] = $cp_option;
            $ladder_action = $teams[$tm]["params"]["cp_bible"][$cp_number]["options"][$cp_option];
            $comment = "You have gone<b> $ladder_action </b>to <b>Level $cp_option </b>";
            //set the new CP availability
            if($pl == $user_ID){
                //update this CP's options
                $teams[$tm]["params"]["cp_bible"][$cp_number]["options"] = $level_options[$cp_number][$cp_option];
                //update all CP's availability
                foreach ($teams[$tm]["params"]["cp_bible"] as &$checkpoint) {
                        if(in_array($checkpoint["cp"],$level_bible[$cp_option])){
                            $checkpoint["available"] = true;
                        } 
                            else {
                                $checkpoint["available"] = false;
                            }
                    }
            }
        }
        //trapdoor
        if($cp['type'] == 'trapdoor'){ 
            if($puzzle_answer == $cp["puzzle_a"]){
            $teams[$tm]["params"]["cp_bible"][$cp_number]["type"] = "ladder";
            $teams[$tm]["params"]["cp_bible"][$cp_number]["puzzle"] = false;
            $teams[$tm]["params"]["cp_bible"][$cp_number]["message"] = "You're in a room with a ladder, only one thing to do";
            $teams[$tm]["params"]["cp_bible"][$cp_number]["options"] = $level_options[$cp_number][$players[$pl]["inventory"]["level"]];
            $comment = "You solved the riddle, the padlock comes off and the ladder gives you a way up";                 
        } else {
            $comment = "The padlock rattles but doesn't budge, that's not the answer";
             };
         }
        //nim
        if($cp['type'] == 'nim'){
            
            if($cp_option == 1){
                $cp_int_number = intval($cp_number);
                if($players[$pl]["inventory"]["block"] > 0){
                $comment = "you can't pick-up another block"; //good
                } else {
                if($teams[$tm]["params"]["nim"][$cp_int_number] != []){
                $held = min($teams[$tm]["params"]["nim"][$cp_number]);
                $players[$pl]["inventory"]["block"] = $held;
                $nim_key = array_search($held,$teams[$tm]["params"]["nim"][$cp_number]);
               if($nim_key !== false){
                unset($teams[$tm]["params"]["nim"][$cp_number][$nim_key]);
                }
                $comment =  "you picked up block $held";
                }
            else {
                $comment = "No blocks to pick-up here";
            }}
            }
            if($cp_option == 2){
                if($players[$pl]["inventory"]["block"] == 0) {
                    $comment = "You don't have a block to place";
                } elseif (!empty($teams[$tm]["params"]["nim"][$cp_number]) && $players[$pl]["inventory"]["block"] > min($teams[$tm]["params"]["nim"][$cp_number])
                        ) {
                            $comment = "you can't place a larger block on a smaller one";
                } else {
                    $teams[$tm]["params"]["nim"][$cp_number][] = $players[$pl]["inventory"]["block"];
                    $players[$pl]["inventory"]["block"] = 0;
                    $comment = "block placed";
                    //check for specials...
                    if(count($teams[$tm]["params"]["nim"][$cp_number]) === 4 && $cp_number == 43 && $teams[$tm]["params"]["nim"]["gold"] > 0) {
                        $comment .= ". You scramble up the blocks and reach on top of the bookshelf and find <b>25 Gold!</b>";
                        $teams[$tm]["params"]["score"] += 25;
                        $teams[$tm]["params"]["cp_bible"][$cp_number]["message"] = "Aside from a bookcase the room is empty, it looks like a good place to put a block down.";
                        $teams[$tm]["params"]["nim"]["gold"] = 0;
                    }
                    if (count($teams[$tm]["params"]["nim"][$cp_number]) > 0 && $cp_number == 41){
                             $teams[$tm]["params"]["cp_bible"][$cp_number]["type"] = "ladder";
                            $teams[$tm]["params"]["cp_bible"][$cp_number]["message"] = "You're in a room with a ladder, only one thing to do";
                            $teams[$tm]["params"]["cp_bible"][$cp_number]["options"] = $level_options[$cp_number][$players[$pl]["inventory"]["level"]];        
                            $comment .= ". You scramble up the blocks and untie the ladder!";
                        
                    }
                }
            }
            $image_array = $teams[$tm]["params"]["nim"][$cp_number];
            rsort($image_array);
            if(count($image_array) > 0){
            $image_string = "nim_".implode("", $image_array).".png";
            $debug_log["image-string"] = $image_string;
            $teams[$tm]["params"]["cp_bible"][$cp_number]["image"] = [1,$image_string];
            } else {
            $teams[$tm]["params"]["cp_bible"][$cp_number]["image"] = [0,""];
            }

        }
        

        //pairs
        if($cp['type'] == 'pair'){ 
            if($cp_number == $players[$pl]["params"]["last_pair"]) {
                $comment = "You can't press the same button twice";
            } else {
                $played_word = strtolower($teams[$tm]["params"]["cp_bible"][$cp_number]["name"]);
                if($teams[$tm]["params"]["pairs"]["time"] + 10 < $t) {
                    $comment = "you have 60s to complete the word.";
                    $teams[$tm]["params"]["pairs"]["time"] = $t;
                    $teams[$tm]["params"]["pairs"]["word"] = $played_word;
                    $comment .= " Current word: $played_word";
                } else {
                    $compound_word = $teams[$tm]["params"]["pairs"]["word"].$played_word;
                    if(in_array($compound_word,$teams[$tm]["params"]["pairs_codes_used"])){
                        $comment = "Code already used";
                        $teams[$tm]["params"]["pairs"]["time"] = $t;
                        $teams[$tm]["params"]["pairs"]["word"] = "";
                    } elseif (in_array($compound_word,$teams[$tm]["params"]["pairs_codes"])) {
                        $comment = "Passcode found: $compound_word";
                        $teams[$tm]["params"]["pairs"]["time"] = $t;
                        $teams[$tm]["params"]["pairs"]["word"] = "";
                        $teams[$tm]["params"]["pairs_found"] += 1;
                        if($teams[$tm]["params"]["pairs_found"] == 3) {
                            $comment .= " <br>Staircase unlocked";
                            $teams[$tm]["params"]["cp_bible"][27]["type"] = "ladder";
                            $teams[$tm]["params"]["cp_bible"][27]["message"] = "You're in a room with a ladder, only one thing to do";
                            $teams[$tm]["params"]["cp_bible"][27]["options"] = $level_options[27][$players[$pl]["inventory"]["level"]];
                            $debug_log["staircase_drama"] = $level_options[27][$players[$pl]["inventory"]["level"]];
                        }} else {
                            $comment = "$compound_word isn't a passcode. Current word: $played_word";
                            $teams[$tm]["params"]["pairs"]["time"] = $t;
                            $teams[$tm]["params"]["pairs"]["word"] = $played_word;
                        }
                    }
                }
            }

        //quest
        if($cp['type'] == 'quest'){
            //princess
            if($cp_number == 51){
                if($cp_option == 1){
                    $teams[$tm]["params"]["cp_bible"][$cp_number]["message"] = '"Thank you!" cries the princess, "please use this box to catch him". And she gives you a cat-box...';
                    $teams[$tm]["params"]["cp_bible"][$cp_number]["options"] = [];        
                    $teams[$tm]["params"]["cp_bible"][12]["options"] =  [1 => "Pick-up mittens"];
                    $teams[$tm]["params"]["cp_bible"][12]["type"] = "quest";
                    $players[$pl]["inventory"]["catbox"] = 1;
                    $comment = "<b>Quest: Find and return mittens!</b>";
                };
                if($cp_option == 2){
                    if($players[$pl]["inventory"]["cats"] == "mittens") {
                    $teams[$tm]["params"]["cp_bible"][$cp_number]["message"] = 'Mittens!" cries the princess, and she hands you <b>75 gold coins</b>.';
                    $teams[$tm]["params"]["cp_bible"][$cp_number]["options"] = []; 
                    $teams[$tm]["params"]["cp_bible"][$cp_number]["image"] = [1,"mittens-found.png"];
                    $teams[$tm]["params"]["score"] += 75;    
                    $comment = "Quest complete: Mittens returned";
                    } else {
                        $comment = "You are not carrying mittens";
                    }
                }
            }
            //mittens collect
            if($cp_number == 12){
                $catbox = $players[$pl]["inventory"]["catbox"];
                if (isset($catbox) && $catbox == 1) {
                $players[$pl]["inventory"]["catbox"] = 0;
                $players[$pl]["inventory"]["cats"] = "mittens";
                $teams[$tm]["params"]["cp_bible"][$cp_number]["message"] = 'Just an empty room now';
                $teams[$tm]["params"]["cp_bible"][$cp_number]["options"] = [];  
                $teams[$tm]["params"]["cp_bible"][51]["options"] =  [2 => "Return mittens"];
                $comment = "mittens collected!";
                } else {
                    $comment = "you need a box to put Mittens in";
                }
            }

        //chef
            if($cp_number == 53){
                if($cp_option == 1){
                    $teams[$tm]["params"]["cp_bible"][$cp_number]["message"] = '"Get out of my kitchen!" Screams the chef';
                    $teams[$tm]["params"]["cp_bible"][$cp_number]["options"] = [];        
                    $teams[$tm]["params"]["cp_bible"][31]["options"][2] =  "Throw the sausages";
                    $teams[$tm]["params"]["cp_bible"][31]["type"] = "quest";
                    $players[$pl]["inventory"]["sausages"] = 3;
                    $comment = "<b>Sausages collected</b>";
                };
            }
            //dog
            if($cp_number == 31){
                if($cp_option == 2){
                $sausages = $players[$pl]["inventory"]["sausages"] ?? 0;
                if ($sausages > 1) {
                $players[$pl]["inventory"]["sausages"] = 0;
                $teams[$tm]["params"]["cp_bible"][$cp_number]["message"] = 'The dog eats the sausages and falls asleep - the bag of gold is now within reach';
                $teams[$tm]["params"]["cp_bible"][$cp_number]["options"] = [1 => "grab the gold"];
                $teams[$tm]["params"]["dog"] = true;
                $comment = "Sausages thrown";
                    } else {
                        $comment = "You don't have any sausages...";
                    }
                }
                if($cp_option == 1){
                    if(isset($teams[$tm]["params"]["dog"]) && $teams[$tm]["params"]["dog"] == true){
                        $comment = "You grab a bag containing 50 gold pieces!";
                        $teams[$tm]["params"]["dog"] = false;
                        $teams[$tm]["params"]["score"] += 50;
                        $teams[$tm]["params"]["cp_bible"][$cp_number]["message"] = 'There is nothing here now except a hungry looking dog';
                        $teams[$tm]["params"]["cp_bible"][$cp_number]["options"] = [];
                    } else {
                        $comment = "<b>No chance - not with that hungry dog there!</b>";
                    }
                }
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
                    foreach ($teams[$tm]["params"]["cp_bible"] as &$checkpoint) {
                        if(in_array($checkpoint["cp"],$level_bible[1])){
                            $checkpoint["available"] = true;} 
                            else {
                                $checkpoint["available"] = false;
                            }
                    }
                    unset($checkpoint);
                    $teams[$tm]["params"]["cp_bible"][999]["available"] = false;
                    $teams[$tm]["params"]["cp_bible"][999]["options"] = [];
                    $comment = "game started.";
                
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