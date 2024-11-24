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

echo json_encode($room);
exit;
?>