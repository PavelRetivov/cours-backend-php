<?php

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $json = file_get_contents('php://input');
    $jsonDecode = json_decode($json, true);
    $mainTodo = file_get_contents('todo.json');
    $mainTodoDecode = json_decode($mainTodo, true);


    $id = $jsonDecode['id'];
    $text = $jsonDecode['text'];
    $checked = $jsonDecode['checked'];

   foreach ($mainTodoDecode['items'] as &$item) {
       if($item['id'] == $id){
              $item['text'] = $text;
              $item['checked'] = $checked;
              file_put_contents('todo.json', json_encode($mainTodoDecode, JSON_PRETTY_PRINT));
              echo json_encode(['status' => '200 OK', 'message' => true]);
              break;
       }
   }
}