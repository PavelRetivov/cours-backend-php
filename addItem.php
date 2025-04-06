<?php

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $requestString = file_get_contents('php://input');
    $request = json_decode($requestString, true);
    $toDoListFileContent = file_get_contents('todo.json');
    $toDoList = json_decode($toDoListFileContent, true);
    $iteratorFileContent = file_get_contents('iterator.json');
    $iterator = json_decode($iteratorFileContent, true);

    if(!isset($request['text']) || strlen($request['text']) === 0){
        echo '400 Bad Request';

        return;
    }

    $toDoList['items'][] = [
        'id' => ++$iterator,
        'text' => $request['text'],
        'checked' => false
    ];
    $toDoListFileContent = json_encode($toDoList, JSON_PRETTY_PRINT);
    file_put_contents('iterator.json', $iterator);
    $result = file_put_contents('todo.json', $toDoListFileContent);

    if(!$result){
        return '500 Internal Server Error';
    }

    echo json_encode(['status' => '200 OK', 'message' => $iterator]);

    return;
}

echo '400 Bad Request';