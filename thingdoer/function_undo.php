/* track wake up time */

<?PHP

echo "purpose - 3c'";
$get_last = "SELECT max(uid) from $table ORDER BY uid DESC";
echo $get_last;
$result = $conn->query($get_last);
echo mysqli_num_rows($result);
echo 'tip';
if (mysqli_num_rows($result) > 0) {
    echo 'pop';
    //print_r(array_keys($result));
    while($row = mysqli_fetch_array($result)) {
        $last = $row[0];}
        echo "found something";
    }
    else {
        echo "found nothing";
    }

//echo $check_last;


$delete_last = "DELETE FROM $table WHERE `uid` = $last";
echo $delete_last;
$undo = $conn->query($delete_last);

?>