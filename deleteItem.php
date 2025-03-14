<?php

if($_SERVER['REQUEST_METHOD'] == 'GET') {

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $todoData = file_get_contents('todo.json');
    $todoDataDecode = json_decode($todoData, true);

    $deleteId = $data['id'];

    $todoDataDecode['items'] = array_filter($todoDataDecode['items'], function($item) use ($deleteId) {
        return $item['id'] != $deleteId;
    });

    $todoDataDecode['items'] = array_values($todoDataDecode['items']);

    file_put_contents('todo.json', json_encode($todoDataDecode));

    echo json_encode($todoDataDecode);
}