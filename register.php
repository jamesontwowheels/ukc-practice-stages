<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="main.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
</head>
<body>
    <h2>MINDGAMES</h2>

    <div class="login-container">
        <div class="login-form smallem">
<?php
// PHP Data Objects(PDO) Sample Code:
try {
    $conn = new PDO("sqlsrv:server = tcp:aarc-server.database.windows.net,1433; Database = aarc_db", "aarc_admin", "aZ158Ja^tR9g6PA6LBj");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $debug_log[] = "db-connected ";
}
catch (PDOException $e) {
    $debug_log[] = 'broken';
    print("Error connecting to SQL Server.");
    die(print_r($e));
}

function generateSimplePassword() {
    // Arrays of adjectives and nouns
    $adjectives = array("Big", "Small", "Bright", "Dark", "Fast", "Slow", "Happy", "Sad", "Red", "Blue", "Green", "Yellow");
    $nouns = array("Cat", "Dog", "Bird", "Fish", "Tiger", "Elephant", "Lion", "Bear", "Wolf", "Fox", "Rabbit", "Horse");

    // Select random adjective and noun
    $adjective = $adjectives[array_rand($adjectives)];
    $noun = $nouns[array_rand($nouns)];

    // Combine the two to form a password
    $password = $adjective . $noun;

    return $password;
}

// Example usage
$this_password = generateSimplePassword();
echo "<h3>Success</h3><br>";
echo "Your password: <br><h3>" . $this_password. "</h3><br>please remember this";
$insert_password = strtolower($this_password);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    $stmt->bindParam(':name', $_POST['name']);
    $stmt->bindParam(':email', $_POST['email']);
    $stmt->bindParam(':password', $this_password); // Not hashing the password /shrug
    if ($stmt->execute()) {

        $to = $_POST['email'];  // The recipient's email address
        $subject = "MindGames Registration";        // The subject of the email
        $message = "You have registered for MindGames, your password is <h3>$this_password</h3> <br>You can login here: https://aarcseries.azurewebsites.net";
        $send_mail = mail($to,$subject,$message);
    } else {
        echo "Error inserting record."; //not sure why this is failing...
    }
?>
<br>
<a href="index.php"><h3>login</h3></a>
</div>
</div>
</body>




