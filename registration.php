<?php

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $mainData = file_get_contents('data.json');
    $mainDataDecode = json_decode($mainData, true);

    $login = $data['login'];
    $password = $data['password'];

    if($login && $password) {
        $data = [
            'login' => $login,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];
        $mainDataDecode[] = $data;

        $jsonData = json_encode($mainDataDecode, JSON_PRETTY_PRINT);
        $result = file_put_contents('data.json', $jsonData);
        if($result){
            echo '200 OK';
        }else{
            echo '500 Internal Server Error';
        }
    }else {
        echo '400 Bad Request';
    }
}
