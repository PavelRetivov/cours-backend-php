<?php
session_start();
require_once (__DIR__ . '/helper.php');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestString = file_get_contents('php://input');
    $requestArray = json_decode($requestString, true);

    if(!isset($requestArray['login']) && !isset($requestArray['password'])) {
        echo '400 Bad Request';

        return;
    }

    $requestLogin = $requestArray['login'];
    $requestPassword = $requestArray['pass'];

    $conn = connect();

    if (!$conn) {
        echo '500 Internal Server Error';

        return;
    }

    if(!$requestLogin || !$requestPassword) {
        echo"status: 400 Bad Request, message: login or password is empty";

        return;
    }

    try{
     $sql = "SELECT * FROM users WHERE login = ? ";
     $stmt = $conn->prepare($sql);
     $stmt->bind_param("s", $requestLogin);

     if(!$stmt->execute()) {
         echo 'Error: ' . $conn->error;

         return;
     }

     $user = $stmt->get_result();

     if($user->num_rows !== 1) {
         echo 'incorrect login or password';

         return;
     }

     $user= $user->fetch_assoc();
     $userPassword = $user['password'];

     if(!password_verify($requestPassword, $userPassword)) {
         echo 'incorrect login or password';

         return;
     }

     $_SESSION['user_id'] = $user['id'];

     echo json_encode(["ok" => true]);

     return;
    }catch (Exception $e) {
        echo '500 Internal Server Error';

        return;
    }
}

echo '400 Bad Request';