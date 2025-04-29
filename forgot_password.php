<?php
// Database connection
require 'db_connect.php'; // assumes $conn is your PDO connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    if (empty($email)) {
        echo "Email is required.";
        exit;
    }

    // Look up the user
    $stmt = $conn->prepare("SELECT name, password FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        echo "No account found for that email.";
        exit;
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $name = $user['name'];
    $password = $user['password'];

    // Send email using Logic App
    $data = [
        "to" => "james.ontwowheels@gmail.com", //$email,
        "subject" => "Your MindGames Password",
        "message" => "<p>Hello $name,</p><p>Your password is: <strong>$password</strong></p>"
    ];

    $ch = curl_init("https://prod-25.uksouth.logic.azure.com:443/workflows/41dce93671ae4941a987c27ea016454a/triggers/When_a_HTTP_request_is_received/paths/invoke?api-version=2016-10-01&sp=%2Ftriggers%2FWhen_a_HTTP_request_is_received%2Frun&sv=1.0&sig=Br-opJh-SEGXYtTiXyLugz29-B-uFzEcuJKaOj0dlUk");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    curl_close($ch);

    header("Location: forgotten_password.php?success=1");
    exit;

} else {
    echo "Invalid request.";
}
