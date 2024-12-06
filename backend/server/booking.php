<?php
require_once '../database/book-model.php';
require_once '../database/room-model.php';
require_once '../db-connection.php';
require_once '../utils/helpers.php';

header('Content-Type: application/json');
date_default_timezone_set('Asia/Bahrain');
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);

        // Check if required fields are set
        if (!isset($data['roomID']) || !isset($data['startTime']) || !isset($data['duration'])) {
            throw new Exception('Missing required fields.');
        }

        $roomID = $data['roomID'];
        $startTime = $data['startTime']; // Ensure start time is in 'YYYY-MM-DD HH:MM:SS' format
        $duration = $data['duration'];

        // Authorization check
        $id = isAuthorized();
        if (!$id) {
            throw new Exception('User is not authorized.');
        }

        // Validate the startTime format (optional)
        $startTimeDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $startTime);
        if (!$startTimeDateTime || $startTimeDateTime->format('Y-m-d H:i:s') !== $startTime) {
            throw new Exception('Invalid start time format.');
        }

        // Create room model and calculate end time
        $roomModel = new RoomModel($pdo, $roomID);
        $endTime = calculateEndTime($startTime, $duration);

        // Save the booking
        $bookModel = new BookModel($pdo, $id, $roomID, null, $startTime, $endTime, null);

        $result = $bookModel->save();
        // echo $result;

        if ($result === true) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception($result);
        }
    } catch (Exception $e) {
        // Return JSON error message
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    // Invalid request method
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}


function calculateEndTime($startTime, $duration) {
  // Convert the start time to a DateTime object
  $startDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $startTime);
  
  if (!$startDateTime) {
      throw new Exception('Invalid start time format.');
  }

  $endDateTime = clone $startDateTime;
  $endDateTime->modify("+$duration minutes");
  
  // Return the end time in 'Y-m-d H:i:s' format
  return $endDateTime->format('Y-m-d H:i:s');
}