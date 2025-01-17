<?PHP
session_start();
$db_response = [];
//generic CP hit
$cp = $_REQUEST["cp"];
$cp_option_choice = $_REQUEST["cp_option_choice"];
$user_input = isset($_REQUEST['user_input']) ? $_REQUEST['user_input'] : '';

//trim whitespace
$input = trim($user_input);
// Sanitize using filter_var
$sanitized_input = filter_var($input, FILTER_SANITIZE_STRING); // Note: FILTER_SANITIZE_STRING is deprecated as of PHP 8.1
// Safely escape for HTML output to prevent XSS
$safe_input = htmlspecialchars($sanitized_input, ENT_QUOTES, 'UTF-8');


$db_response[] = "You hit CP $cp";
include 'db_connect.php';
$user_ID = $_SESSION['user_ID'];
$location = $_SESSION['location'];
$game = 5;
$input_time = time();

$sql = "INSERT INTO dbo.test_game (Player_ID, CP_ID, Time_stamp, location, game, puzzle_answer, option) VALUES 
    ($user_ID, $cp, $input_time, $location, $game, '$safe_input', $cp_option_choice);";
$db_response[] = $sql;
if ($conn->query($sql) == TRUE) {
    $db_response[] =  "record inserted successfully";
    $last_id = $conn->insert_id;
    $db_response[] = "The success inserted ID is: " . $last_id . "<br>";
} else {
    $db_response[] = "Error: " . $sql . "<br>" . $conn->error;
    $db_response[] = "The last inserted ID is: " . $last_id . "<br>";
}

//include game rules + return something to the player