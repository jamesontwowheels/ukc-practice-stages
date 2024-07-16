<?PHP
$cp = $_REQUEST["cp"];
echo "You hit CP $cp";

include 'db_connect.php';

$sql = "INSERT INTO `dbo.test_game` VALUES 
    (1, 1, 2, 3);";

if ($conn->query($sql) === TRUE) {
    echo "record inserted successfully";
} else {
    echo "record insert failed";
}