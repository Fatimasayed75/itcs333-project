<?php
require_once '../database/comment-model.php';
require_once '../db-connection.php';
require_once '../utils/helpers.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            echo json_encode(['success' => false, 'message' => 'Invalid JSON input.']);
            exit;
        }

        $bookingID = $data['bookingID'] ?? null;
        $roomID = $data['roomID'] ?? null;
        $content = $data['comment'] ?? null;
        $userID = isAuthorized();

        if (!$bookingID || !$roomID || !$content || !$userID) {
            echo json_encode(['success' => false, 'message' => 'Invalid input.']);
            exit;
        }

        $commentModel = new CommentModel($pdo);
        $commentModel->userID = $userID;
        $commentModel->roomID = $roomID;
        $commentModel->content = $content;

        $result = $commentModel->save();

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save comment.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
