<?PHP
$cp = $_REQUEST["cp"];
echo "You hit CP $cp";
echo "extended echo";
include 'db_connect.php';

echo "here";

$sql = "INSERT INTO dbo.test_game (Player_ID, CP_ID, Time_stamp) VALUES 
    (1, 2, 3);";
echo $sql;
if ($conn->query($sql) === TRUE) {
    echo "record inserted successfully";
} else {
    echo "record insert failed";
}