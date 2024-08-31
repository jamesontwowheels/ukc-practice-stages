<?PHP
// session_start();
// $user_ID = $_SESSION['user_ID'];
$debug = 1;
$response = [];
$debug_log = [];
$commentary = [];
$debug_log[] = "data play";

echo 'this';

include 'db_connect.php';

ini_set("allow_url_fopen", 1);
//Get event results from DB:

$query = "select * from dbo.test_game where Player_ID = $user_ID ORDER BY Time_stamp ASC";
$result = $conn->query($query);

$i = 0;

//build punches list
$player_cps = [];
$players = [];



foreach ($result as $row) {
    if(!in_array($row["Player_ID"],$players)){
        $players[] = $row["Player_ID"];
        $player_cps[] = $row["Player_ID"];
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
    $words = ["THUNDER","HUNTED","HUNTED","HURDEN","HUNTER","RETUND","RUNTED","TURNED","DERTH","UNETH","DRENT","NUDER","RUNED","TENDU","TREND","TRUED","TUNED","UNDER","UNRED","URNED","TUNER","URENT","HEND","HERD","HUED","THUD","HENT","HERN","HUER","HUNT","HURT","RUTH","TEHR","THEN","THRU","DENT","DERN","DUET","DUNE","DUNT","DURE","DURN","NERD","NUDE","NURD","REND","RUDE","RUED","RUND","TEND","TUND","TURD","UNDE","URDE","RENT","RUNE","RUNT","TERN","TRUE","TUNE","TURN","DUH","EDH","ETH","HEN","HER","HET","HUE","HUN","HUT","NTH","REH","THE","DEN","DUE","DUN","END","NED","RED","RUD","TED","URD","ERN","NET","NUR","NUT","REN","RET","RUE","RUN","RUT","TEN","TUN","URE","URN","UTE"];
    $cps_letters = [1,2,3,4,5,6,7];
    $cps_bonus = [11,12];
    $all_cps = [1,2,3,4,5,6,7,11,12,20,999];
    $word = ["","N","D","R","T","H","U","E"];
    $word_value = [0,1,4,1,1,2,1,1];
    $word_length_value = [0,0,0,0,4,8,13,20];
    $word_count_bonus = [0,0,0,5,10,20,30,45,60,80,100];

    //special CPS;
    $cp_wsf = 20;
    $cp_start_finish = 999;

    $cp_names = [
        1 => "N",
        2 => "D",
        3 => "R",
        4 => "T",
        5 => "H",
        6 => "U",
        7 => "E",
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
    //values
    $stage_time = 60*60;
