<?php

if($_SERVER['REQUEST_METHOD'] == "GET") {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $mainTodo = file_get_contents('todo.json');

    echo $mainTodo;
}