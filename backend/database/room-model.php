<?php

// comment this in case we won't use the Crud class
require_once __DIR__ . '/../utils/crud.php';
use Utils\Crud;

class RoomModel
{
    // a struct that we can use.

    private $conn;
    private $crud;
    public $roomID;
    public $type;
    public $capacity;
    public $isAvailable;
    public $floor;

    public function __construct($dbConnection, $roomID = null, $type = "class", $capacity = null, $isAvailable = false, $floor = null)
    {
        $this->conn = $dbConnection;
        $this->crud = new Crud($dbConnection);
        $this->roomID = $roomID;
        $this->type = $type;
        $this->capacity = $capacity;
        $this->isAvailable = $isAvailable;
        $this->floor = $floor;
    }
    // end of the struct

    // Save, function to save a room to the database
    public function save()
    {
        $crud = new Crud($this->conn);
        $columns = ['roomID', 'type', 'capacity', 'isAvailable', 'floor'];
        $values = [$this->roomID, $this->type, $this->capacity, $this->isAvailable, $this->floor];
        return $crud->create('room', $columns, $values);
    }

    // Update, function to update a room to the database
    public function update()
    {
        $crud = new Crud($this->conn);
        $updates = ['type' => $this->type, 'capacity' => $this->capacity, 'isAvailable' => $this->isAvailable, 'floor' => $this->floor];
        $condition = 'roomID = ?';
        return $crud->update('room', $updates, $condition, $this->roomID);
    }

    // Delete, function to delete a room from the database
    public function delete()
    {
        $crud = new Crud($this->conn);
        $condition = 'roomID = ?';
        return $crud->delete('room', $condition, $this->roomID);
    }

    // Get a room by ID
    public function getRoomById($id)
    {
        return $this->crud->read('room', [], 'roomID = ?', $id);
    }

    // Get all rooms
    public function getAllRooms()
    {
        return $this->crud->read('room');
    }

    // Get all available rooms
    public function getAvailableRooms()
    {
        $condition = 'isAvailable = ?';
        return $this->crud->read('room', [], $condition, true);
    }

    // Get all rooms by type
    public function getRoomsByType($type)
    {
        $condition = 'type = ?';
        return $this->crud->read('room', [], $condition, $type);
    }

    // Get all rooms by floor
    public function getRoomsByFloor($floor)
    {
        $condition = 'floor = ?';
        return $this->crud->read('room', [], $condition, $floor);
    }

    // Get all rooms by capacity
    public function getRoomsByCapacity($capacity)
    {
        $condition = 'capacity = ?';
        return $this->crud->read('room', [], $condition, $capacity);
    }

    // Get all rooms by type and floor
    public function getRoomsByTypeAndFloor($type, $floor)
    {
        $condition = 'type = ? AND floor = ?';
        return $this->crud->read('room', [], $condition, $type, $floor);
    }

    // get all rooms by user id by the crud class
    public function getRoomsByUserId($userId)
    {
        $condition = 'roomID IN (SELECT roomID FROM bookings WHERE userID = ?)';
        return $this->crud->read('room', [], $condition, $userId);
    }

    // get bookings by room id
    public function getBookingsByRoomId($roomId)
    {
        $condition = 'roomID = ?';
        return $this->crud->read('bookings', [], $condition, $roomId);
    }

    // get available time slots for a room
    // booked when
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





}