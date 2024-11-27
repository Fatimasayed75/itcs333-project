<?php
require_once '../database/comment-model.php';
require_once '../database/book-model.php';
require_once '../utils/helpers.php';

require_once '../db-connection.php';

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

        // Save the comment first
        $commentModel = new CommentModel($pdo);
        $commentModel->userID = $userID;
        $commentModel->roomID = $roomID;
        $commentModel->content = $content;
        $commentModel->save();

        // Update feedback status for the booking
        $bookingModel = new BookModel($pdo, $bookingID, $userID, $roomID, $data['bookingTime'], $data['startTime'], $data['endTime']);
        $bookingModel->submitFeedback(); // This should set the feedback status to 1

        // Return the updated feedback status
        echo json_encode(['success' => true, 'feedbackExists' => true]);

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

