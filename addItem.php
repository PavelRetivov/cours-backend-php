<?php

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $mainData = file_get_contents('todo.json');
    $mainDataDecode = json_decode($mainData, true);
    $iterator = file_get_contents('iterator.json');
    $iteratorDecode = json_decode($iterator, true);

    if($data['text']){
        $mainDataDecode['items'][] = [
            'id' => $iteratorDecode,
            'text' => $data['text'],
            'checked' => false
        ];
        $iteratorDecode++;
        $jsonData = json_encode($mainDataDecode, JSON_PRETTY_PRINT);
        $iteratorJsonData = json_encode($iteratorDecode, JSON_PRETTY_PRINT);
        file_put_contents('iterator.json', $iteratorJsonData);
        $result = file_put_contents('todo.json', $jsonData);
        if($result){
            echo json_encode(['status' => '200 OK', 'message' => $iteratorDecode]);
        }else{
            echo '500 Internal Server Error';
        }
        return;
    }else {
        echo '400 Bad Request';
        return;
    }
}