<?php
require_once (__DIR__ . '/helper.php');

if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $conn = connect();

    if (!$conn) {
        echo '500 Internal Server Error';

        return;
    }

    $requestString = file_get_contents('php://input');
    $requestArray = json_decode($requestString, true);
    $deleteId = $requestArray['id'];

    if (!$deleteId) {
        echo '400 Bad Request';

        return;
    }

    try{
        $sql = "DELETE FROM todo_tasks WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $deleteId);
        if(!$stmt->execute()) {
            echo 'Error: ' . $conn->error;

            return;
        }

        disconnect($conn);
        echo json_encode(["status" => "200 OK", "ok" => true]);

        return;
    }catch (Exception $e) {
        echo '500 Internal Server Error';

        return;
    }
}