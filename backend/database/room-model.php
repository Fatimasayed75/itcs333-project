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

}