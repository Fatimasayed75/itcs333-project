<?php
// Disable error display in production
ini_set('display_errors', 0);
error_reporting(E_ALL);

// return available time slots for a room
require '../db-connection.php';
require '../database/room-model.php';

header('Content-Type: application/json');

// Check if the 'roomID' and 'date' are set in the query string
if (!isset($_GET['roomID']) || !isset($_GET['date'])) {
  echo json_encode(['error' => 'Room ID and Date are required']);
  exit;
}

$roomID = $_GET['roomID'];
$date = $_GET['date'];

$roomModel = new RoomModel($pdo);
$room = $roomModel->getRoomById($roomID);

$availableTimes = $roomModel->getAvailableTimeSlots($roomID, $date);

if ($availableTimes === null) {
  echo json_encode(['error' => 'No available times found']);
} else {
  echo json_encode($availableTimes);
}

exit;

?>

