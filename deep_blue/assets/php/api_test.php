<?PHP
ini_set("allow_url_fopen", 1);
//Get event results using maprun API:
$event_name = "Deep%20Blue%20SCOREQ90%20PZ";
$api_url = "https://p.fne.com.au:8886/resultsGetPublicForEvent?eventName=";
$url_live = $api_url . $event_name;
//commented out for local testing:
//file_put_contents("results.json", file_get_contents($url_live)); 
//$url = "results.json";
// this saves the file locally, so can use for caching results... 
$obj = json_decode(file_get_contents($url_live), true);
$count_results = count($obj['results']);
$results = $obj['results'];
$x = 0;



//set-up the static constants (each requires it's own rule...):
    //Bulk CPS
    $cps_treasure = [11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29];
    $cps_fish = [31,32,33,34];
    $cps_oxygen = [102,202];
    //special CPS;
    $cp_trident = 333;
    $cp_start_finish = 999;
    $cp_poseidons_gamble = 666;
    $cp_dive_boat = 777;
    //results catchers
    $results_detailed = [];
    $results_summary = [];
    $results_ids= [];
    $results_names= [];
    //values
    $fish = 5;
    $treasure = 10;
    $stage_time = 75*60;

//start looping the contestants:
while($x < $count_results){
    $result = $results[$x];

    $name = $result['Firstname'];
    $surname = $result['Surname'];
    $finish_time = intval($result['TotalTimeSecs']);
    //check for time penalties:
        if($finish_time > $stage_time){
            $time_penalty = floor(($finish_time-$stage_time)/3);
        } else {$time_penalty = 0;}
    $x += 1;
//set-up course/result variables for each contestants
    $id = $result['Id'];
    $results_ids[] = $id;
    $results_names[$id] = [$name,$surname];
    $results_detailed[$id] = [];
    $results_summary[$id] = [];
    $count_cps = count($result['Punches']);
    $y = 0;
    $cps = [];
    $times = [];
    $oxygen = 0;
    $inventory = [];
    $spear = 0;
    $bank = [];
    $multiplier = 1;
    $running_score = 0;

//build and order the punches list:
    while ($y < $count_cps){
        $cps[] = preg_replace("/[^0-9]/", "",$result['Punches'][$y]["ControlId"]);
        $times[] = intval($result['Punches'][$y]["TimeAfterStartSecs"]);
        $pr = $player_results = [];
        $y += 1;
    }
    array_multisort($times, $cps);

    // cycle through the punch list;
    $z = 0;
    
    while ($z < count($cps)){

        // add to detailed results = $results_detailed[$id][] = [_your code_];
        // add to summary results = $results_summary[$id][] = [_your code_];

        $cp = $cps[$z];
        $t = $times[$z];
        $z += 1;

        //add oxygen
        if (in_array($cp,$cps_oxygen)){
            $oxygen = $t + 600;
            $results_detailed[$id][] = [$t,$cp,"Oxygen picked up",0,$running_score];
        } 

        //add spear
        if ($cp == $cp_trident){
            $spear = 1;
            $results_detailed[$id][] = [$t,$cp,"Trident collected!",0,$running_score];
        }

        //collect treasure
        if (in_array($cp,$cps_treasure)){
            if ($t > $oxygen){
                $results_detailed[$id][] = [$t,$cp,"Oh no, out of oxygen! You've dropped everything",0,$running_score];
                $inventory = [];
            } elseif (in_array($cp,$inventory)){
                $results_detailed[$id][] = [$t,$cp,"Treasure already held",0,$running_score];
            }elseif (in_array($cp,$bank)){
                $results_detailed[$id][] = [$t,$cp,"Treasure already in the bank",0,$running_score];
            } else {
                $results_detailed[$id][] = [$t,$cp,"Treasure ".$cp." picked-up",0,$running_score];
                $inventory[] = $cp;
            }            
        }

        //collect fish:
        if (in_array($cp,$cps_fish)){
            //check spear:
            if($spear == 0){
                $results_detailed[$id][] = [$t,$cp,"You tried to pick-up fish $cp with no trident",0,$running_score];
            } elseif (in_array($cp,$inventory)){
                $results_detailed[$id][] = [$t,$cp,"Fish already caught this trip",0,$running_score];
            } elseif ($t > $oxygen){
                $results_detailed[$id][] = [$t,$cp,"Oh no, out of oxygen! You've dropped everything",0,$running_score];
                $inventory = [];
            } else {
                $results_detailed[$id][] = [$t,$cp,"Fish $cp speared!",0,$running_score];
                $inventory[] = $cp;
            }            
        }

        //take gamble
        if ($cp == $cp_poseidons_gamble){
            $threshold = 80 * $multiplier;
            if($running_score >= $threshold){
            $running_score -= $threshold;
            $results_detailed[$id][] = [$t,$cp,"Gamble taken!",-$threshold,$running_score];
            $multiplier += 1;
            $bank = [];
            } else {
                $results_detailed[$id][] = [$t,$cp,"You don't have enough treasure to pay Poseidon, no gamble taken",0,$running_score];
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
                    $results_detailed[$id][] = [$t,$cp,"Fish $item landed!",$value,$running_score];
                } elseif (in_array($item,$cps_treasure)){
                    $value = $treasure * $multiplier;
                    $running_score += $value;
                    $results_detailed[$id][] = [$t,$cp,"Treasure ".$item." stashed!",$value,$running_score];
                    $bank[] = $item;
                }
                $i += 1;
            }
            // empty inventory:
            $inventory = [];
            $spear = 0;
        }
         



        //start_finish

        //

    }
    $final_score = $running_score - $time_penalty;
   $results_summary[$id][] = [$name,$surname,$time,$running_score,-$time_penalty,$final_score,$id];
}

$r = 0;

