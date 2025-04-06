<?php

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $requestChangeTodoTask = file_get_contents('php://input');
    $changeTodoTask = json_decode($requestChangeTodoTask, true);
    $todoTasksFileContent = file_get_contents('todo.json');
    $todoTasks = json_decode($todoTasksFileContent, true);

    if(!isset($changeTodoTask['id']) || !isset($changeTodoTask['text'])
        || !isset($changeTodoTask['checked'])) {
        echo '400 Bad Request';

        return;
    }

    if(!filter_var($changeTodoTask['checked'], FILTER_VALIDATE_BOOLEAN)){
        echo '400 Bad Request - checked must be a boolean';

        return;
    }

    $id = $changeTodoTask['id'];
    $text = $changeTodoTask['text'];
    $checked = $changeTodoTask['checked'];

    $index = array_search($id, array_column($todoTasks['items'], 'id'));

    if($index === false) {
        echo '404 Not Found';

        return;
    }

    $todoTasks['items'][$index]['text'] = $text;
    $todoTasks['items'][$index]['checked'] = $checked;
    file_put_contents('todo.json', json_encode($todoTasks, JSON_PRETTY_PRINT));

    echo json_encode(["ok"=>true]);

    return;
}

echo '400 Bad Request';