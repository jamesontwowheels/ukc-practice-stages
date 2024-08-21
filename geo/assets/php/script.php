<?PHP
$db_response = [];
//generic CP hit
$cp = $_REQUEST["cp"];
$db_response[] = "You hit CP $cp";
include 'db_connect.php';
$user_ID = $_SESSION['user_ID'];

$sql = "INSERT INTO dbo.test_game (Player_ID, CP_ID, Time_stamp) VALUES 
    ($user_ID, $cp, 3);";
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