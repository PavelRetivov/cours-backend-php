<?php
session_set_cookie_params([
    'lifetime' => 86400,  // Час життя кукі (1 день)
    'path' => '/',
    'domain' => 'mysite.local', // Замініть на ваш домен
    'secure' => true,    // Має бути true, якщо HTTPS
    'httponly' => true,
    'samesite' => 'None'
]);
session_start();
require_once (__DIR__ . '/helper.php');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestString = file_get_contents('php://input');
    $requestArray = json_decode($requestString, true);

    if(empty($requestArray['login']) && empty($requestArray['password'])) {
        http_response_code(400);
        echo '400 Bad Request';

        return;
    }

    $requestLogin = $requestArray['login'];
    $requestPassword = $requestArray['pass'];

    $conn = connect();

    if (!$conn) {
        http_response_code(500);
        echo 'Error: sorry database don`t working';

        return;
    }

    if(!$requestLogin || !$requestPassword) {
        http_response_code(400);
        echo"status: 400 Bad Request, message: login or password is empty";

        return;
    }

    try{
        $sql = "SELECT * FROM users WHERE login = ? ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $requestLogin);

        if(!$stmt->execute()) {
            http_response_code(500);
            echo 'Error: sorry database don`t working';

            return;
        }

        $user = $stmt->get_result();

        if($user->num_rows !== 1) {
            http_response_code(400);
            echo 'incorrect login or password';

            return;
        }

        $user= $user->fetch_assoc();
        $userPassword = $user['password'];

        if(!password_verify($requestPassword, $userPassword)) {
            http_response_code(400);
            echo 'incorrect login or password';

            return;
        }

        session_regenerate_id();
        $_SESSION['user_id'] = $user['id'];
        http_response_code(200);
        echo json_encode(["ok" => true]);

        return;
    }catch (Exception $e) {
        http_response_code(500);
        echo 'Error: sorry database don`t working';

        return;
    }
}else if($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
    http_response_code(400);
    echo '400 Bad Request';

    return;
}