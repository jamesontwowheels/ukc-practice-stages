// fetch_session.php (server-side script to return session variable)
<?php
session_start();
echo json_encode(['location' => $_SESSION['location']]);
?>
