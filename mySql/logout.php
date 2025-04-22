<?php

session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_destroy();
    http_response_code(200);
    echo json_encode(['ok' => true]);

    return;
}else if($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
    http_response_code(400);
    echo '400 Bad Request';

    return;
}