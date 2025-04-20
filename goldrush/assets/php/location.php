<?php
session_start();
echo json_encode(['location' => $_SESSION['location']]);

