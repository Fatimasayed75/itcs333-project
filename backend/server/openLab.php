<?php
require_once '../database/book-model.php';
require_once '../db-connection.php';
require_once '../utils/helpers.php';

header('Content-Type: application/json');
date_default_timezone_set('Asia/Bahrain');
ini_set('display_errors', 1);
error_reporting(E_ALL);


$data = json_decode(file_get_contents('php://input'), true);

if ($data === null) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    exit;
}

// Validate input
if (!isset($data['bookingID'], $data['status'], $data['roomID'], $data['bookingTime'], $data['startTime'], $data['endTime'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$bookingID = $data['bookingID'];
$status = $data['status'];
$roomID = $data['roomID'];
$bookingTime = $data['bookingTime'];
$startTime = $data['startTime'];
$endTime = $data['endTime'];
$userID = $data['userID'];

$bookingModel = new BookModel($pdo, $userID, $roomID, $bookingTime, $startTime, $endTime, $bookingID);

if ($status === 'approved') {
    $status = 'active';
    $response = $bookingModel->updateStatus($status);
} else if ($status === 'rejected') {
    $status = 'rejected';
    $response = $bookingModel->updateStatus($status);
}


// Return success or failure response
if ($response) {
    echo json_encode(['status' => 'success', 'message' => 'Booking status updated']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update booking status']);
}
?>
