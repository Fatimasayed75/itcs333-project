<?php
require_once '../database/book-model.php';
require_once '../db-connection.php';

header('Content-Type: application/json');

// Check if POST request and booking ID are present
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bookingID'])) {
    $bookingID = $_POST['bookingID'];
    $bookModel = new BookModel($pdo, null, null, null, null, null, null);

    // delete the booking
    if ($bookModel->delete($bookingID)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Unable to cancel booking.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
