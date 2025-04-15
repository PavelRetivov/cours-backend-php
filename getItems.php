<?php

if($_SERVER['REQUEST_METHOD'] == "GET") {
    echo file_get_contents('todo.json');

    return;
}

echo '400 Bad Request';