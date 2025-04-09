<?php
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_destroy();
    echo json_encode(['ok' => true]);
}

echo '400 Bad Request';