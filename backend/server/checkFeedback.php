<?php
require_once '../database/comment-model.php';
require_once '../db-connection.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $bookingID = $_GET['bookingID'] ?? null;

        if (!$bookingID) {
            echo json_encode(['success' => false, 'message' => 'Booking ID is required.']);
            exit;
        }

        // Check if feedback exists for this booking
        $commentModel = new CommentModel($pdo);
        $feedbackExists = $commentModel->feedbackExists($bookingID); // Add this method in CommentModel

        echo json_encode(['success' => true, 'feedbackExists' => $feedbackExists]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
