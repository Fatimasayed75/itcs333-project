<?php
require_once '../../database/connection.php';
require_once '../../database/room-model.php';

header('Content-Type: application/json');

try {
    // Get database connection
    $conn = getConnection();

    // Get POST data
    $roomID = $_POST['roomID'];
    $roomName = $_POST['roomName'];
    $department = $_POST['department'];
    $floor = (int)$_POST['floor'];
    $capacity = (int)$_POST['capacity'];
    $roomType = $_POST['roomType'];

    // Create new room instance
    $room = new RoomModel(
        $conn,
        $roomID,
        $roomType,
        $capacity,
        true, // isAvailable by default
        $floor
    );

    // Save the room
    $result = $room->save();

    if ($result) {
        http_response_code(201);
        echo json_encode(['success' => true, 'message' => 'Room added successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add room']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
