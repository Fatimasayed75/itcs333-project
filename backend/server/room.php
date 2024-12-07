<?php
require_once '../database/room-model.php';
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
    // Create room model instance with connection
    $room = new RoomModel($pdo);
    
    switch ($action) {
        case 'add':
            // Validate required fields
            if (!isset($data['capacity'], $data['roomID'])) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                exit;
            }

            if (!preg_match('/^S40-(0[0-9]{1,2}[1-9]{1}|1[0-9]{2}[1-9]{1}|2[0-9]{2}[1-9]{1})$/', $data['roomID'])) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid room ID format']);
                exit;
            }

            $floor = $data['roomID'][4]; // S40-0XX | S40-1XXX | S40-2XXX

            if($data['capacity'] < 20 || $data['capacity'] > 200) {
                echo json_encode(['status' => 'error', 'message' => 'Capacity must be between 20-200']);
                exit;
            }

            $isExist = $room->getRoomById($data['roomID']);

            if(!empty($isExist)) {
                echo json_encode(['status' => 'error', 'message' => 'Room is already exists']);
                exit;
            }
            

            // Create new room instance with all data
            $room = new RoomModel(
                $pdo,
                $data['roomID'], // roomID will be auto-generated
                $data['type'] ?? 'class',
                (int)$data['capacity'],
                true,
                $floor ?? '0'
            );
            
            // Save room
            if ($room->save()) {
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Room added successfully',
                    'roomID' => $room->roomID
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add room']);
            }
            break;

        case 'edit':
            // Validate required fields
            if (!isset($data['roomID'], $data['capacity'], $data['floor'])) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                exit;
            }
            
            // Check if room exists
            if (!$room->isRoomExists($data['roomID'])) {
                echo json_encode(['status' => 'error', 'message' => 'Room not found']);
                exit;
            }
            
            // Create room instance with updated data
            $room = new RoomModel(
                $pdo,
                $data['roomID'],
                $data['type'] ?? 'class',
                (int)$data['capacity'],
                isset($data['isAvailable']) ? (bool)$data['isAvailable'] : true,
                (int)$data['floor']
            );
            
            // Update room
            if ($room->update()) {
                echo json_encode(['status' => 'success', 'message' => 'Room updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update room']);
            }
            break;

        case 'delete':
            // Validate required fields
            if (!isset($data['roomID'])) {
                echo json_encode(['status' => 'error', 'message' => 'Room ID is required']);
                exit;
            }
            
            // Check if room exists
            if (!$room->isRoomExists($data['roomID'])) {
                echo json_encode(['status' => 'error', 'message' => 'Room not found']);
                exit;
            }
            
            $room = new RoomModel($pdo, $data['roomID']);
            
            // Delete room
            if ($room->delete()) {
                echo json_encode(['status' => 'success', 'message' => 'Room deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete room']);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    error_log("Error in room.php: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error occurred']);
}