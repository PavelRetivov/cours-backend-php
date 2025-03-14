<?php

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $mainData = file_get_contents('data.json');
    $mainDataDecode = json_decode($mainData, true);

    $login = $data['login'];
    $password = $data['password'];

    if($login && $password) {
        foreach ($mainDataDecode as $dataUser) {
            $dataLogin = $dataUser['login'];
            $dataPassword = $dataUser['password'];

            if($login == $dataLogin){
                if(password_verify($password, $dataPassword)){
                    session_start();
                    $_SESSION['login'] = $login;
                    echo json_encode(['status' => '200 OK', 'message' => true]);
                }else{
                    echo json_encode(['status' => '400 Bad Request', 'message' => false ]);
                }
                return;
            }
        }
        echo  json_encode(['status' => '400 Bad Request', 'message' => false]);
        return;
    }

    echo json_encode(['status' => '400 Bad Request', 'message' => false]);
}
