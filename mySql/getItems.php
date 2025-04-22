<?php
session_start();

require_once (__DIR__ . '/helper.php');

if($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conn = connect();
    $userId = $_SESSION['user_id'];

    if(!$conn) {
        http_response_code(500);
        echo 'Error: sorry database don`t working';

        return;
    }

    try {
        $sql = "SELECT * FROM todo_tasks WHERE user_id = ?";
        $smth = $conn->prepare($sql);
        $smth->bind_param("i", $userId);

        if(!$smth->execute()) {
            http_response_code(500);
            echo 'Error: sorry database don`t working';

            return;
        }

        $todoTaskUserMysqlResult = $smth->get_result();
        disconnect($conn);
    }catch (Exception $e) {
        http_response_code(500);
        echo 'Error: sorry database don`t working';

        return;
    }

    $todoTaskUser['items'] = array_map(function($items){
        unset($items['user_id']);
        return [...$items, 'checked' => $items['checked'] == 1 ? true : false];
    },$todoTaskUserMysqlResult->fetch_all(MYSQLI_ASSOC));

    if (!$todoTaskUser) {
        http_response_code(500);
        echo 'Error: sorry database don`t working';

        return;
    }

    echo json_encode($todoTaskUser);
    return;
}else if($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
    http_response_code(400);
    echo '400 Bad Request';

    return;
}