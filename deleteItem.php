<?php

if($_SERVER['REQUEST_METHOD'] == 'GET') {

    $requestTaskId = file_get_contents('php://input');
    $taskId = json_decode($requestTaskId, true);
    $todoTasksFileContent = file_get_contents('todo.json');
    $todoTasks = json_decode($todoTasksFileContent, true);

    if (!isset($taskId['id']) || !is_numeric($taskId['id'])) {
        echo '400 Bad Request';

        return;
    }

    $deleteId = (int)$taskId['id'];
    $countStartTodoTasks = count($todoTasks['items']);
    $todoTasks['items'] = array_filter($todoTasks['items'], fn($item) => $item['id'] !== $deleteId);
    $todoTasks['items'] = array_values($todoTasks['items']);
    $result = file_put_contents('todo.json', json_encode($todoTasks));

    if(!$result){
        echo '500 Internal Server Error';

        return;
    }

    echo $countStartTodoTasks === count($todoTasks['items']) ? json_encode(["not ok" => false]) : json_encode(["ok" => true]);

    return;
}

echo '400 Bad Request';