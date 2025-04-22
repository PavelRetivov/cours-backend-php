<?php
session_start();
require_once (__DIR__ . '/helper.php');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = connect();

    if (!$conn) {
        echo '500 Internal Server Error';

        return;
    }

    $requestString = file_get_contents('php://input');
    $requestArray = json_decode($requestString, true);
    $requestAddItem = $requestArray['text'];
    $userId = $_SESSION['user_id'];

    if (empty($requestAddItem) || empty($userId)) {
        http_response_code(400);
        echo $userId . '400 Bad Request';

        return;
    }
    try {
        $sql = "INSERT INTO todo_tasks (text, checked, user_id) VALUES (?, false, ?)";
        $stmt = $conn->prepare($sql);
        $user = 2;
        $stmt->bind_param("si", $requestAddItem, $userId);

        if (!$stmt->execute()) {
            http_response_code(500);
            echo 'Error: sorry database don`t working';

            return;
        }

        http_response_code(200);
        echo json_encode(["status" => "200 OK", "id" => $conn->insert_id]);
        disconnect($conn);

        return;
    } catch (Exception $e) {
        http_response_code(500);
        echo 'Error: sorry database don`t working';

        return;

    }
}else if($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
    http_response_code(400);
    echo '400 Bad Request';

    return;
}