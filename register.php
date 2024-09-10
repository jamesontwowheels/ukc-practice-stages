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
echo "Generated Password: " . generateSimplePassword();
/*
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($conn->real_escape_string($_POST['password']), PASSWORD_BCRYPT); // Hash the password

    // SQL query to insert data into the users table
    $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
*/

