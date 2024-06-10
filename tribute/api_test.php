<?PHP

//***code test options */
$debug = 1;
$local_test = 1;


if($debug == 1) {echo "2";}
ini_set("allow_url_fopen", 1);
//Get event results using maprun API:
$event_name = "pacman-v2%20SCOREQ75";
$api_url = "https://p.fne.com.au:8886/resultsGetPublicForEvent?eventName=";
$url_live = $api_url . $event_name;
//commented out for local testing:
// full_url = https://p.fne.com.au:8886/resultsGetPublicForEvent?eventName=Uno%20SCOREQ90%20PZ
//file_put_contents("results.json", file_get_contents($url_live)); 
if ($local_test == 1) {$url_live = "results_test.json";}
// this saves the file locally, so can use for caching results... 
$obj = json_decode(file_get_contents($url_live), true);
$count_results = count($obj['results']);
$results = $obj['results'];
$x = 0;
$next_team = 500;

// prefix for QR codes: http://www.maprunners.com.au?c=
// PIN for event is 9722
if($debug == 1) {echo " 2 ";}
//set-up the static constants (each requires it's own rule...):
    //Event Bulk CPs ***EDIT THIS***
        //coords
        $cps_coords = [10,11,12,13,14,15,16,17,18,19];
        
        //scrabble
        $cps_letters = [1,2,3,4,5];
        $cps_bonus = [28,29];

        //GP
        $cps_gp = [41,42,43,44];

    
    //Event reference information ***EDIT THIS***
        //coords
        $coords_value = [1,1,2,2,3,3,4,4,5,5];

        //scrabble
        $words =["AMENT","MANET","MEANT","MENTA","AMEN","ANTE","ETNA","MANE","MATE","MEAN","MEAT","MENT","META","NAME","NEAT","NEMA","TAME","TANE","TEAM","AME","ANE","ANT","ATE","EAN","EAT","ETA","MAE","MAN","MAT","MEN","MET","MNA","NAE","NAM","NAT","NET","TAE","TAM","TAN","TEA","TEN"];
        $word = ["","A","E","M","N","T"];
        $word_value = [0,1,1,3,1,1];
        $word_length_value = [0,0,0,0,4,8,13,20];
        $word_count_bonus = [0,0,0,5,10,20,30,45,60,80,100];

    //Event Special CPs ***EDIT THIS***
    $cp_wsf = 20;
    $cp_braincell_correct = 71;
    $cp_braincell_incorrect = 72;
    $cp_next_stage = 100;
    $cp_start_finish = 999;
    $cp_time_god = 900;

    //values ***EDIT THIS****
    $stage_time = 90*60;
    $penalty_rate = 2; 
    //seconds per point lost

    //BAU Bulk CPs
    $cps_teams = [401,402,403,404,405,406,407,408,409,410,411,412,413,414,415,416,417,418,419,420,421,422,423,424,425,426,427,428,429,430,431,432,433,434,435,436,437,438,439,440];
    
    //results catchers
    $results_detailed = [];
    $results_summary = [];
    $results_ids= [];
    $results_names= [];

    //player/team catcher
    $players = [];
    $teams = [];
    if($debug == 1) {echo " 65 ";}

    $tg = 0;
    //pre-loop to set the time god
    while ($tg < $count_results){
        $result = $results[$tg];
        $name = $result['Firstname'];
        $surname = $result['Surname'];
        if($surname == "Fraser"){
            //work out start time in seconds:
            $start_local = $result['StartPunchTimeLocal'];
            $start_hours = intval(substr($start_local,0,2));
            $start_minutes = intval(substr($start_local,3,2));
            $start_seconds = intval(substr($start_local,6,2));
            $global_start_time = (($start_hours * 60 + $start_minutes) * 60 + $start_seconds); 
        }

        if($debug == 1) {echo " Global $name $surname start time = $global_start_time ";}
        
        $tg += 1;
    }

    //start looping the contestants:
    while($x < $count_results){
    $result = $results[$x];
    $team = 0;
    
    //work out start time in seconds:
         $start_local = $result['StartPunchTimeLocal'];
         $start_hours = intval(substr($start_local,0,2));
         $start_minutes = intval(substr($start_local,3,2));
         $start_seconds = intval(substr($start_local,6,2));
         $start_time = (($start_hours * 60 + $start_minutes) * 60 + $start_seconds);

         $global_start_lag = $global_start_time-$start_time; //add this on to prevent penalties
    

         if($debug == 1) {echo " global start lag = $global_start_lag ";}

    $name = $result['Firstname'];
    $surname = $result['Surname'];
    $finish_time = intval($result['TotalTimeSecs']) - $global_start_lag;
    //check for time penalties:
        if($finish_time > $stage_time){
            $time_penalty = floor(($finish_time-$stage_time)/$penalty_rate);
        } else {$time_penalty = 0;}
    $x += 1;

//set-up course/result variables for each contestants
    
    $id = $result['Id'];  

    $count_cps = count($result['Punches']);
    $y = 0;
    $cps = [];
    $times = [];

//build and order the punches list:
    while ($y < $count_cps){
        $new_cp = preg_replace("/[^0-9]/", "",$result['Punches'][$y]["ControlId"]);
        $cps[] = $new_cp;
        $times[] = intval($result['Punches'][$y]["TimeAfterStartSecs"]) + $start_time;
        $pr = $player_results = [];
        $y += 1;
    }
    array_multisort($times, $cps);
$b = 0;
while ($b < count($cps)){
    if(in_array($cps[$b],$cps_teams)){
        $team = $cps[$b];
    } else {};
    $b += 1;
}

if($team > 1){ //all good
} else { $team = $next_team;
            $next_team += 1;}

//create the player:
$player = [$name,$surname,$cps,$times,$team,$time_penalty,$start_time,$finish_time];
$players[] = $player;
}

if($debug == 1) {echo " 122 ";}
//build teams
$c = 0;
$teams_used = [];
while($c < count($players)){
    $d = $players[$c];
    $d_team = $d[4];
    if(in_array($d_team,$teams_used)){
        $d_name = $d[0]." ".substr($d[1],0,1);
        $d_cps = $d[2];
        $d_times = $d[3];
        $d_penalty = $d[5];
        $d_start = $d[6];
        $d_end = $d[7];

        //$e = existing
        $e = $teams[$d_team];
        $teams[$d_team][0] = $e[0]." & ".$d_name; 
        $teams[$d_team][1] = array_merge($e[1],$d_cps);        
        $teams[$d_team][2] = array_merge($e[2],$d_times);        
        $teams[$d_team][3] = max($e[3],$d_penalty);       
        $teams[$d_team][4] = min($e[4],$d_start); 
        $teams[$d_team][5] = min($e[5],$d_end);
        array_multisort($teams[$d_team][2],$teams[$d_team][1]);
    } else { 
        $teams_used[] = $d_team;
        $d_name = $d[0]." ".substr($d[1],0,1);
        $d_cps = $d[2];
        $d_times = $d[3];
        $d_penalty = $d[5];
        $d_start = $d[6];
        $d_end = $d[7];
        //global time override !!!!!!!!!
        $d_start = $global_start_time;
        $teams[$d_team] = [$d_name,$d_cps,$d_times,$d_penalty,$d_start,$d_end];
     }
     $c += 1;
}

/////going to need to cycle through players here, and drag down the results collectors!!!////
//$e = each team
$e = 0;


if($debug == 1) {echo " 193-start looping $teams_used teams ";}

while($e < count($teams_used)){
    $id = $teams_used[$e];
    $result = $teams[$id];
    $team_name = "Team $id";
    $cps = $result[1];
    $times = $result[2];
    $time_penalty = $result[3];
    $results_ids[] = $id;
    $name = $result[0];
    $team_start = $result[4];
    $team_end = $result[5];
    $results_names[$id][0] = $result[0];
    $results_detailed[$id] = [];
    $results_summary[$id] = [];
    $running_score = 0;

    //player specific event ***EDIT THIS***
        $current_stage = 1;

        //Coordination specifics
        $coords_used = [];
        $last_coord_time = -100;
        $last_coord_cp = 0;

        //SCRABBLE SPECIFICS
        $used_bonuses = [];
        $used_letters = [];
        $used_words = [];
        $current_word = "";
        $current_word_value = 0;
        $current_bonus = 1;

        //BRAINCELL SPECIFICS
        $braincell_level = 1;
        $braincell_active = 1;
        $braincell_time = 0;

        //GP SPECIFICS
        $gp_1_next = 1;
        $gp_2_next = 1;
        
    // cycle through the punch list;
    $z = 0;
   $count_of_cps = count($cps);
if($debug == 1) {echo " 236-start looping count($count_of_cps) teams ";}

    while ($z < count($cps)){
        // add to detailed results = $results_detailed[$id][] = [_your code_];
        // add to summary results = $results_summary[$id][] = [_your code_];

        $cp = $cps[$z];
        $t = $times[$z] - $team_start;
        $z += 1;

       //insert Checkpoint rules here ***EDIT THIS***

       if($debug == 1) {echo " 243, $cp clear braincell ";}
            //BRAINCELL

            if($braincell_active = 1){
                $braincell_elapsed = $t - $braincell_time;
                if($cp == $cp_braincell_correct){
                    //braincell passed
                    $braincell_active = 0;
                    $results_detailed[$id][] = [$t,$cp,"Braincell passed in $braincell_elapsed seconds",0,$running_score];
                } elseif ($cp == $cp_braincell_incorrect) {
                    //braincell failed
                    $braincell_active = 0;
                    $braincell_penalty = 600 - $braincell_elapsed;
                    $running_score -= $braincell_penalty;
                    $results_detailed[$id][] = [$t,$cp,"Braincell failed in $braincell_elapsed seconds. $braincell_penalty penalty seconds",0,$running_score];
                } elseif (in_array($cp,$cps_teams)){
                    //ignore this.
                } else {
                    //braincell skipped
                    $braincell_active = 0;
                    $braincell_penalty = 900 - $braincell_elapsed;
                    $running_score -= $braincell_penalty;
                    $results_detailed[$id][] = [$t,$cp,"Braincell skipped $braincell_elapsed seconds. $braincell_penalty penalty seconds",0,$running_score];
                }
            }

            if($cp == $cp_next_stage){
                $braincell_active = 1;
                $braincell_time = $t;
                $current_stage += 1;
                $results_detailed[$id][] = [$t,$cp,"Next Stage! Entering Stage $current_stage",0,$running_score];
            }

            if($debug == 1) {echo " 274 start coordinate ";}
        if($current_stage == 1){
            //COORDINATE
            if(in_array($cp,$cps_coords)){
                if (in_array($cp,$coords_used)){
                    $results_detailed[$id][] = [$t,$cp,"checkpoint already collected",0,$running_score];
                } else {
                    $coords_used[] = $cp;
                    $coord_gap = $t - $last_coord_time;
                    
                    $cp_val_check = $cp - 10;
                    $this_val = $coords_value[$cp_val_check];
                    if($coord_gap < 30){
                        $cp_val_check = $cp - 10;
                        if($this_val == $last_coord_val){    
                            $running_score += 450;
                            $results_detailed[$id][] = [$t,$cp,"$coord_gap second gap, coordinated and matching collection, 7m30 bonus ",0,$running_score]; 
                        }else {
                            $running_score += 300;
                        $results_detailed[$id][] = [$t,$cp,"$coord_gap second gap, coordinated collection, 5m bonus",0,$running_score]; 
                        }
                    } else {
                        $results_detailed[$id][] = [$t,$cp,"$coord_gap second gap, no bonus",0,$running_score]; 
                    }
                    $last_coord_time = $t;
                    $last_coord_val = $this_val;
                }
            }

        }

        if($debug == 1) {echo " 305, start scrabble ";}
        if($current_stage == 2){
                //SCRABBLE
                
if($debug == 1) {echo " 316 - in scrabble ";}
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
        }

        if($current_stage == 3){
            
if($debug == 1) {echo " 316 - in GP ";}
            if(in_array($cp,$cps_gp)){
            $this_gp = $cp - 30;
            if($this_gp == $gp_1_next){
                $gp_1_next += 1;
                $results_detailed[$id][] = [$t,$cp,"GP Checkpoint $this_gp cleared (1/2)","",$running_score];
            } elseif ($this_gp == $gp_2_next){
                $gp_2_next +1;
                $results_detailed[$id][] = [$t,$cp,"GP Checkpoint $this_gp cleared (2/2)","",$running_score];
            } else {
                $results_detailed[$id][] = [$t,$cp,"Wrong GP Checkpoint visited, looking for $gp_1_next or $gp_2_next","",$running_score];
            }
            }
        }
        
       //END OF CHECKPOINT RULES// 
       
if($debug == 1) {echo " End checkpoint rules ";}        
    }
    //can put some finish line rules in here
        //SCRABBLE FINISH
        $words_found = count($used_words);
        $wf_bonus = $word_count_bonus[$words_found];
        $running_score += $wf_bonus;
        $results_detailed[$id][] = [$t,$cp,"$words_found words found, + $wf_bonus bonus","",$running_score];
        
        //END SCRABBLE FINISH//
    
        //GRAND PRIX FIN

        if($gp_1_next == 5 && $gp_2_next == 5){
            $results_detailed[$id][] = [$team_end,"","Grand Prix completed","",$running_score];
        } else {
            $running_score -= 900;
            $results_detailed[$id][] = [$team_end,"","Grand Prix incomplete - 15 minute penalty","",$running_score];
        }

        //END GRAND PRIX FINISH
    
        //END FINISH RULES

        $results_detailed[$id][] = [$team_end,$cp,"Finish tagged","",$running_score];

$final_score = $running_score - $time_penalty + $team_end;
$results_summary[$id][] = [$name,$team_name,$time,$running_score,-$time_penalty,$final_score,$id];

 $e += 1; 

$r = 0;
};