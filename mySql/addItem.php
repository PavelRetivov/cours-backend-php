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

    if (!$requestAddItem || !$userId) {
        echo '400 Bad Request';

        return;
    }
    try {
        $sql = "INSERT INTO todo_tasks (text, checked, user_id) VALUES (?, '0', ?)";
        $stmt = $conn->prepare($sql);
        $user = 2;
        $stmt->bind_param("si", $requestAddItem, $userId);

        if (!$stmt->execute()) {
            echo 'Error: ' . $conn->error;

            return;
        }

        echo json_encode(["status" => "200 OK", "id" => $conn->insert_id]);
        disconnect($conn);

        return;
    } catch (Exception $e) {
        echo $conn->error;

        return;

    }
}

echo '400 Bad Request';