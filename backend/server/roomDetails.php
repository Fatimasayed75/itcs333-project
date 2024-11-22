<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../db-connection.php';
require '../database/room-model.php';

header('Content-Type: application/json');

$roomID = $_GET['roomID']; // Example: S40-028

$roomModel = new RoomModel($pdo);
$room = $roomModel->getRoomById($roomID);

echo json_encode($room);
exit;
?>