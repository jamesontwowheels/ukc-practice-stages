<?php
session_start();
session_unset();
session_destroy();

// Redirect to a page that clears localStorage
header("Location: logout.html");
exit;
?>
