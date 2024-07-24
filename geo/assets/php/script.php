<?PHP

//generic CP hit
$cp = $_REQUEST["cp"];
echo "You hit CP $cp";
include 'db_connect.php';

$sql = "INSERT INTO dbo.test_game (Player_ID, CP_ID, Time_stamp) VALUES 
    (1, $cp, 3);";
echo $sql;
if ($conn->query($sql) == TRUE) {
    echo "record inserted successfully";
    $last_id = $conn->insert_id;
    echo "The success inserted ID is: " . $last_id . "<br>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    echo "The last inserted ID is: " . $last_id . "<br>";
}

//include game rules + return something to the player