<?php

require_once (__DIR__ . '/helper.php');

if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $conn = connect();

    if (!$conn) {
        http_response_code(500);
        echo '500 Internal Server Error';

        return;
    }

    $requestString = file_get_contents('php://input');
    $requestArray = json_decode($requestString, true);
    $deleteId = $requestArray['id'];

    if (empty($deleteId)) {
        http_response_code(400);
        echo '400 Bad Request';

        return;
    }

    try{
        $sql = "DELETE FROM todo_tasks WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $deleteId);
        if(!$stmt->execute()) {
            http_response_code(500);
            echo 'Error: sorry database don`t working';

            return;
        }

        disconnect($conn);
        echo json_encode(["status" => "200 OK", "ok" => true]);

        return;
    }catch (Exception $e) {
        http_response_code(500);
        echo 'Error: sorry database don`t working';

        return;
    }
}else if($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
    http_response_code(400);
    echo '400 Bad Request';

    return;
}