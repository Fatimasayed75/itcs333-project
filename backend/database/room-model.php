<?php

require_once __DIR__ . '/../utils/crud.php';
require_once __DIR__ . '/../utils/constants.php';

use Utils\Crud;

class RoomModel
{
    private $conn;
    public $roomID;
    public $type;
    public $capacity;
    public $isAvailable;
    public $floor;

    public function __construct($conn, $roomID = null, $type = "class", $capacity = null, $isAvailable = false, $floor = null)
    {
        $this->conn = $conn;
        $this->roomID = $roomID;
        $this->type = $type;
        $this->capacity = $capacity;
        $this->isAvailable = $isAvailable;
        $this->floor = $floor;
    }

    // Generate a unique room ID
    private function generateRoomID()
    {
        $crud = new Crud($this->conn);
        $prefix = 'R';
        $floor = str_pad($this->floor, 2, '0', STR_PAD_LEFT);

        // Get the highest room number for this floor
        $condition = "roomID LIKE ?";
        $pattern = $prefix . $floor . '%';
        $result = $crud->read('room', ['roomID'], $condition, $pattern);

        $maxNum = 0;
        foreach ($result as $row) {
            $num = (int) substr($row['roomID'], 3); // Extract number after floor
            $maxNum = max($maxNum, $num);
        }

        $nextNum = str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
        return $prefix . $floor . $nextNum;
    }

    // Save a new room
    public function save()
    {
        try {
            if ($this->roomID === null) {
                $this->roomID = $this->generateRoomID();
            }

            $crud = new Crud($this->conn);
            $columns = ['roomID', 'type', 'capacity', 'isAvailable', 'floor'];
            $values = [$this->roomID, $this->type, $this->capacity, $this->isAvailable ? 1 : 0, $this->floor];
            return $crud->create('room', $columns, $values);
        } catch (Exception $e) {
            error_log("Error saving room: " . $e->getMessage());
            return false;
        }
    }

    // Update a room
    public function update()
    {
        try {
            $crud = new Crud($this->conn);
            $updates = [
                'type' => $this->type,
                'capacity' => $this->capacity,
                'isAvailable' => $this->isAvailable ? 1 : 0,
                'floor' => $this->floor
            ];
            $condition = 'roomID = ?';
            return $crud->update('room', $updates, $condition, $this->roomID);
        } catch (Exception $e) {
            error_log("Error updating room: " . $e->getMessage());
            return false;
        }
    }

    // Delete a room
    public function delete()
    {
        try {
            $crud = new Crud($this->conn);
            $condition = 'roomID = ?';
            return $crud->delete('room', $condition, $this->roomID);
        } catch (Exception $e) {
            error_log("Error deleting room: " . $e->getMessage());
            return false;
        }
    }

    // Get a room by ID
    public function getRoomById($id)
    {
        $crud = new Crud($this->conn);
        return $crud->read('room', [], 'roomID = ?', $id);
    }

    // Get all rooms
    public function getAllRooms()
    {
        $crud = new Crud($this->conn);
        return $crud->read('room');
    }


    // get all rooms by user id by the crud class
    public function getRoomsByUserId($userId)
    {
        $crud = new Crud($this->conn);
        $condition = 'roomID IN (SELECT roomID FROM bookings WHERE userID = ?)';
        return $crud->read('room', [], $condition, $userId);
    }

    // get bookings by room id
    public function getBookingsByRoomId($roomId)
    {
        $crud = new Crud($this->conn);
        $condition = 'roomID = ?';
        return $crud->read('bookings', [], $condition, $roomId);
    }

    // get available time slots for a room
    public function getAvailableTimeSlots($roomId, $date)
    {
        // Get all bookings for the room
        $roomBookings = $this->getBookingsByRoomId($roomId);

        // Array to store booked times for the given date
        $bookedTimes = [];

        // Time intervals in decimal format for 10 and 30 minutes
        $tenMinutesInDecimal = 10 / 60;  // 10 minutes = 0.1667
        $thirtyMinutesInDecimal = 30 / 60;  // 30 minutes = 0.5

        foreach ($roomBookings as $booking) {
            if ($booking['status'] != 'rejected' && $booking['status'] != 'pending') {
                // Extract the date part of the booking startTime and endTime
                $bookingDate = substr($booking['startTime'], 0, 10);  // Get 'YYYY-MM-DD' from 'YYYY-MM-DD HH:MM:SS'

                // If the booking date matches the provided date, process the booking times
                if ($bookingDate == $date) {
                    // Extract the start time and convert to decimal
                    $startTime = substr($booking['startTime'], 11, 5);  // Get 'HH:MM' part
                    $startHour = (int) substr($startTime, 0, 2);
                    $startMinute = (int) substr($startTime, 3, 2);
                    $startDecimal = $startHour + $startMinute / 60; // Convert start time to decimal format

                    // Extract the end time and convert to decimal
                    $endTime = substr($booking['endTime'], 11, 5);  // Get 'HH:MM' part
                    $endHour = (int) substr($endTime, 0, 2);
                    $endMinute = (int) substr($endTime, 3, 2);
                    $endDecimal = $endHour + $endMinute / 60;  // Convert end time to decimal format

                    // Add all time slots between start and end times as booked
                    for ($i = $startDecimal; $i < $endDecimal; $i += 0.5) {  // Increment by 0.5 for half-hour intervals
                        $bookedTimes[] = number_format($i, 2);  // Format to 2 decimal places (e.g., 8.00, 8.30)
                    }

                    // Add times before and after the booking to account for the 10-minute and 30-minute windows
                    for ($i = $startDecimal - $thirtyMinutesInDecimal; $i < $endDecimal + $tenMinutesInDecimal; $i += 0.5) {  // Adjust intervals to 0.5 for 30-minute slots
                        if ($i >= 0) {  // Ensure time is not negative
                            $bookedTimes[] = number_format($i, 2);  // Format to 2 decimal places
                        }
                    }
                }
            }
        }

        // Array to store available times
        $availableTimes = [];

        // Define the working hours (e.g., from 8:00 AM to 5:30 PM)
        $startTime = 8;    // 8:00 AM
        $endTime = 17.5;   // 5:30 PM (17:30 = 17.5)

        // Iterate over the time slots in decimal format and check availability
        for ($i = $startTime; $i < $endTime; $i += 0.5) {  // Increment by 0.5 for half-hour intervals
            $formattedTime = number_format($i, 2);  // Format current time slot to ensure consistency

            // If the time slot is not in the booked times, add it as available
            if (!in_array($formattedTime, $bookedTimes)) {
                $availableTimes[] = $formattedTime;  // Add formatted available time
            }
        }

        // Return the available times as an array
        return $availableTimes;
    }

    // Check if room exists
    public function isRoomExists($roomID)
    {
        $crud = new Crud($this->conn);
        $condition = 'roomID = ?';
        return !empty($crud->read('room', ['roomID'], $condition, $roomID));
    }

    // get all equibments for a room
    public function getRoomEquibments($roomId)
    {
        $crud = new Crud($this->conn);
        $condition = 'roomID = ?';
        $equibmentsWithIds = $crud->read('room_equipments', ['equipmentID', 'Quantity'], $condition, $roomId);

        foreach ($equibmentsWithIds as &$equibment) {  // Use reference (&) to modify the array directly
            // Get the equipment name and quantity using the equipmentID
            $equibmentData = $this->getEquibmentNameAndQuantity($equibment['equipmentID']);

            // Check if we got valid data and assign them to the current equipment entry
            if (!empty($equibmentData)) {
                $equibment['equipName'] = $equibmentData['equipmentName'];
            }
        }
        return $equibmentsWithIds;
    }

    // Get equipment name and quantity from the equipments table
    public function getEquibmentNameAndQuantity($equipId)
    {
        $crud = new Crud($this->conn);
        $condition = 'equipmentID = ?';
        $result = $crud->read('equipment', [], $condition, $equipId);

        // Return the result, assuming there's only one row for the equipmentID
        return !empty($result) ? $result[0] : [];
    }

    // get all equipments
    public function getAllEquipments()
    {
        $crud = new Crud($this->conn);
        return $crud->read('equipment');
    }


    // insert equipment using crud
    public function insertEquipment($roomID, $equipmentID, $quantity = 10) {
        $crud = new Crud($this->conn);
        $columns = ['roomID', 'equipmentID', 'Quantity'];
        $values = [$roomID, $equipmentID, $quantity];
        return $crud->create('room_equipments', $columns, $values);
    }
}