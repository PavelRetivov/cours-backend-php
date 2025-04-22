<?php

require_once (__DIR__ . '/helper.php');

if($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $conn = connect();

    if (!$conn) {
        http_response_code(500);
        echo '500 Internal Server Error';

        return;
    }

    $requestString = file_get_contents('php://input');
    $requestArray = json_decode($requestString, true);
    $taskId = $requestArray['id'];
    $changeText = $requestArray['text'];
    $changeChecked = $requestArray['checked'];

    if (empty($taskId) || empty($changeText) || isset($changeChecked)) {
        http_response_code(400);
        echo '400 Bad Request';

        return;
    }

    try{
        $sql = "UPDATE todo_tasks SET text = ?, checked = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $changeText, $changeChecked, $taskId);
        if(!$stmt->execute()) {
            http_response_code(500);
            echo 'Error: sorry database don`t working';

            return;
        }

        disconnect($conn);
        http_response_code(200);
        echo json_encode(["status" => "200 OK", "ok" => true]);

        return;
    }catch (Exception $e) {
        http_response_code(500);
        echo '500 Internal Server Error';

        return;
    }
}else if($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
    http_response_code(400);
    echo '400 Bad Request';

    return;
}