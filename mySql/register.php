<?php
session_start();
require_once (__DIR__ . '/helper.php');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestString = file_get_contents('php://input');
    $requestArray = json_decode($requestString, true);

    if(!isset($requestArray['login']) && !isset($requestArray['pass'])) {
        echo '400 Bad Request';
        return;
    }

    $requestLogin = $requestArray['login'];
    $requestPassword = $requestArray['pass'];

    if(!$requestLogin || !$requestPassword) {
        echo"status: 400 Bad Request, message: login or password is empty";
        return;
    }

    $conn = connect();
    try{
        $sqlCheckLogin = "SELECT COUNT(*) FROM users WHERE login = ?";
        $stmt = $conn->prepare($sqlCheckLogin);
        $stmt->bind_param("s", $requestLogin);

        if(!$stmt->execute()) {
            echo 'Error: ' . $conn->error;

            return;
        }

        $count = $stmt->get_result()->fetch_row();

        if($count[0] > 0) {
            echo 'user with this login already exists';

            return;
        }

        $hashedPassword = password_hash($requestPassword, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (login, password) VALUES (?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $requestLogin, $hashedPassword);

        if(!$stmt->execute()) {
        echo 'Error: ' . $conn->error;

            return;
        }

        echo json_encode(['ok' => true]);

        return;
    }catch (PDOException $e){
        die(json_encode(["error" => $e->getMessage()]));
    }
}

echo '400 Bad Request';