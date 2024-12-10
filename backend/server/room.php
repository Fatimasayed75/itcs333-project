<?php
require_once '../database/room-model.php';
require_once '../db-connection.php';
require_once '../utils/helpers.php';
require_once '../utils/constants.php';

use Utils\Constants;

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
            if (!isset($data['NewRoomCapacity'], $data['NewRoomID'], $data['NewRoomFloor'], $data['NewRoomType'], $data['NewRoomDept'])) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                exit;
            }

            $inputRoomID = trim($data['NewRoomID']);

            // Check if room ID starts with S40- followed by 1 to 4 numbers
            if (!preg_match('/^S40-(\d{1,4})$/', $inputRoomID)) {
                echo json_encode(['status' => 'error', 'message' => 'Room ID must start with S40- followed by 1 to 4 numbers']);
                exit;
            }

            // ONLY NUMBER AFTER S40-
            $roomID = str_replace("S40-", "", $inputRoomID);

            // ADD 0s until length is 4
            if (strlen($roomID) < 4) {
                $roomID = str_pad($roomID, 4, "0", STR_PAD_LEFT);
            }

            $insertedRoomId = "S40-" . $roomID;

            $isExist = $room->getRoomById($insertedRoomId);

            if (!empty($isExist)) {
                echo json_encode(['status' => 'error', 'message' => 'Room is already exists']);
                exit;
            }

            if ($data['NewRoomCapacity'] < 20 || $data['NewRoomCapacity'] > 200) {
                echo json_encode(['status' => 'error', 'message' => 'Capacity must be between 20-200']);
                exit;
            }

            $inputDept = strtoupper(trim($data['NewRoomDept']));
            $depts = ['CS', 'IS', 'CE'];

            if (!in_array($inputDept, $depts)) {
                echo json_encode(['status' => 'error', 'message' => 'Department must be CS, IS or CE']);
                exit;
            }

            if ($data['NewRoomFloor'] < 0 || $data['NewRoomFloor'] > 2) {
                echo json_encode(['status' => 'error', 'message' => 'Floor must be between 0-2']);
                exit;
            }

            // Create new room instance with all data
            $room = new RoomModel(
                $pdo,
                $insertedRoomId,
                $data['NewRoomType'] ?? 'class',
                (int) $data['NewRoomCapacity'],
                true,
                $data['NewRoomFloor'],
                $inputDept,
            );

            // check if entered quantities valid
            if (isset($data['quantity'])) {
                foreach ($data['quantity'] as $quantity) {
                    if ($quantity < 1 || $quantity > 100) {
                        echo json_encode(['status' => 'error', 'message' => 'Quantity must be between 1-100']);
                        exit;
                    }
                }
            }

            // Save room
            if ($room->save()) {
                // Save equipment assignments if provided
                if (isset($data['equipment'])) {
                    $equipmentIDs = $data['equipment']; // Array of selected equipment IDs

                    foreach ($equipmentIDs as $equipmentID) {
                        // Check if a custom quantity is provided for this equipment
                        $quantity = isset($data['quantity'][$equipmentID]) ? (int) $data['quantity'][$equipmentID] : 10;
                        $room->insertEquipment($insertedRoomId, $equipmentID, $quantity);
                    }
                }

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
                (int) $data['capacity'],
                isset($data['isAvailable']) ? (bool) $data['isAvailable'] : true,
                (int) $data['floor']
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
    echo json_encode(['status' => 'error', 'message' => 'Server error occurred: ' . $e->getMessage()]);
}