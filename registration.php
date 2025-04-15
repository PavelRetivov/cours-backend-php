<?php

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $requestLoginAndPassword = file_get_contents('php://input');
    $loginAndPassword = json_decode($requestLoginAndPassword, true);
    $usersContentFile = file_get_contents('users.json');
    $users = json_decode($usersContentFile, true);

    if(!isset($loginAndPassword['login']) || !isset($loginAndPassword['password'])) {
        echo '400 Bad Request';

        return;
    }

    $login = $loginAndPassword['login'];
    $password = $loginAndPassword['password'];

    if(!$login || !$password) {
        echo '400 Bad Request';

        return;
    }

    $newUser = [
        'login' => $login,
        'password' => password_hash($password, PASSWORD_DEFAULT)
    ];
    $users[] = $newUser;

    $usersContentFile= json_encode($users, JSON_PRETTY_PRINT);
    $result = file_put_contents('data.json', $usersContentFile);

    echo $result ? json_encode(['status' => '200 OK', 'message' => 'User registered successfully']) : '400 Bad Request';

    return;
}

echo '400 Bad Request';
