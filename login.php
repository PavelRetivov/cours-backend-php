<?php
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $requestAuthorizationData= file_get_contents('php://input');
    $authorizationData= json_decode($requestAuthorizationData, true);
    $usersFileContent = file_get_contents('users.json');
    $users = json_decode($usersFileContent, true);

    if(!isset($authorizationData['login']) || !isset($authorizationData['password'])) {
        echo '400 Bad Request';

        return;
    }

    $login = $authorizationData['login'];
    $password = $authorizationData['password'];

    if(!$login || !$password) {
        echo '400 Bad Request';

        return;
    }

    foreach ($users as $user) {
        $dbLogin = $user['login'];
        $dbPassword = $user['password'];

        if($login === $dbLogin){
            if(password_verify($password, $dbPassword)){
                $_SESSION['user'] = $login;
                echo json_encode(['status' => '200 OK', 'ok' => true]);
            }else{
                echo json_encode(['status' => '400 Bad Request', 'message' => 'Incorrect login or password' ]);
            }
            return;
        }
    }

    echo '400 Bad Request';

    return;
}

echo '400 Bad Request';
