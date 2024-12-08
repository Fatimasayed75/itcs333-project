<?php
require_once '../database/book-model.php';
require_once '../database/user-model.php';
require_once '../db-connection.php';
require_once '../utils/helpers.php';
require_once '../utils/constants.php';

USE Utils\Constants;

header('Content-Type: application/json');
date_default_timezone_set('Asia/Bahrain');

// Get the raw POST data
$data = $_POST;

// Check if user is authorized
$userID = isAuthorized();

// Check if user is admin
if ($userID !== Constants::ADMIN_USER_ID) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Validate action
if (!isset($data['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Action is required']);
    exit;
}

$action = $data['action'];

try {
    // Create booking model instance with connection
    $booking = new BookModel($pdo, null, null, null, null, null, null);
    $room = new RoomModel($pdo);
    $user = new UserModel($pdo);

    
    switch ($action) {
        case 'view':
            // Validate bookingID
            if (!isset($data['bookingID'])) {
                echo json_encode(['status' => 'error', 'message' => 'Booking ID is required']);
                exit;
            }

            // Fetch booking details by ID
            $bookingData = $booking->getBookingById($data['bookingID']);
            if ($bookingData) {
                echo json_encode(['status' => 'success', 'booking' => $bookingData]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Booking not found']);
            }
            break;

        case 'edit':
            // Validate required fields
            if (!isset($data['bookingID'], $data['roomID'], $data['startTime'], $data['endTime'], $data['userID'])) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                exit;
            }

            // Check if booking exists
            if (!$booking->getBookingsBy('bookingID',$data['bookingID'])) {
                echo json_encode(['status' => 'error', 'message' => 'Booking not found']);
                exit;
            }

            // Check if room exists
            if (!$room->isRoomExists($data['roomID'])) {
                echo json_encode(['status' => 'error', 'message' => 'Room not found']);
                exit;
            }

            if (!$user->isUserExist($data['userID'])) {
                echo json_encode(['status' => 'error', 'message' => 'User not found']);
                exit;
            }


            // Create booking instance with updated data
            $updatedBooking = new BookModel(
                $pdo,
                $data['userID'],
                $data['roomID'],
                null,
                $data['startTime'],
                $data['endTime'],
                $data['bookingID']
            );

            $result = $updatedBooking->update();

            // Update booking
            if ($result === true) {
                echo json_encode(['status' => 'success', 'message' => 'Booking updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => $result]);
            }
            break;

        case 'delete':
            // Validate bookingID
            if (!isset($data['bookingID'])) {
                echo json_encode(['status' => 'error', 'message' => 'Booking ID is required']);
                exit;
            }

            // Check if booking exists
            if (!$booking->getBookingsBy('bookingID',$data['bookingID'])) {
                echo json_encode(['status' => 'error', 'message' => 'Booking not found']);
                exit;
            }

            // Delete booking
            if ($booking->delete($data['bookingID'])) {
                echo json_encode(['status' => 'success', 'message' => 'Booking deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete booking']);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    error_log("Error in booking.php: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error occurred']);
}
