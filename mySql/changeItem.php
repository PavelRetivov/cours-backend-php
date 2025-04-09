<?php
require_once (__DIR__ . '/helper.php');

if($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $conn = connect();

    if (!$conn) {
        echo '500 Internal Server Error';

        return;
    }

    $requestString = file_get_contents('php://input');
    $requestArray = json_decode($requestString, true);
    $changeId = $requestArray['id'];
    $changeText = $requestArray['text'];
    $changeChecked = $requestArray['checked'];

    if (!$changeId || !$changeText || $changeChecked === null) {
        echo '400 Bad Request';

        return;
    }

    try{
        $sql = "UPDATE todo_tasks SET text = ?, checked = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $changeChecked = $changeChecked ? 1 : 0;
        $stmt->bind_param("ssi", $changeText, $changeChecked, $changeId);
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

echo '400 Bad Request';