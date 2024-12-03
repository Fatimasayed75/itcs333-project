<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../db-connection.php';
require '../database/room-model.php';

header('Content-Type: application/json');

if(!isset($_GET['roomID'])) {
  echo json_encode(['error' => 'Room ID is required']);
  exit;
}

$roomID = $_GET['roomID'];
$roomModel = new RoomModel($pdo);
$room = $roomModel->getRoomById($roomID);

// Prepare the available time slots
$availableTimeSlots = [];
$startTime = strtotime('08:00');
$endTime = strtotime('19:30');
$durationOptions = [30, 60, 90, 120, 150];

for ($i = $startTime; $i < $endTime; $i += 1800) {
  $time = date('H:i', $i);
  foreach($durationOptions as $duration) {
    $availableTimeSlots[] = ['time' => $time, 'duration' => $duration];
  }
}

$room['availableTimeSlots'] = $availableTimeSlots;

echo json_encode($room);
exit;

?>