<?php
if (isset($_GET['file'])) {
    $file = basename($_GET['file']);
    $path = "game-admin/game-rules/" . $file;

    if (file_exists($path)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    } else {
        http_response_code(404);
        echo "File not found.";
    }
}
