<?php

// comment this in case we won't use the Crud class
require_once __DIR__ . '/../utils/crud.php';
use Utils\Crud;

class RoomModel
{
    // a struct that we can use.
    private $conn;
    public $roomID;
    public $type;
    public $capacity;
    public $isAvailable;
    public $floor;

    public function __construct($dbConnection, $roomID = null, $type = "class", $capacity = null, $isAvailable = false, $floor = null)
    {
        $this->conn = $dbConnection;
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
        $columns = ['type', 'capacity', 'isAvailable', 'floor'];
        $values = [$this->type, $this->capacity, $this->isAvailable, $this->floor];
        return $crud->create('room', $columns, $values);
    }

    // Create a new room in case we won't use the Crud class
    public function createRoom($type, $capacity, $floor)
    {
        $query = "INSERT INTO Room (type, capacity, isAvailable, floor) VALUES (?, ?, false, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sii", $type, $capacity, $floor);
        return $stmt->execute();
    }

    // Update, function to update a room to the database
    public function update()
    {
        $crud = new Crud($this->conn);
        $updates = ['type' => $this->type, 'capacity' => $this->capacity, 'isAvailable' => $this->isAvailable, 'floor' => $this->floor];
        $condition = 'id = ?';
        return $crud->update('room', $updates, $condition, $this->roomID);
    }

    // Update a room by ID
    public function updateRoom($id, $type, $capacity, $isAvailable, $floor)
    {
        $query = "UPDATE Room SET type = ?, capacity = ?, isAvailable = ?, floor = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("siisi", $type, $capacity, $isAvailable, $floor, $id);
        return $stmt->execute();
    }

    // Delete, function to delete a room from the database
    public function delete()
    {
        $crud = new Crud($this->conn);
        $condition = 'id = ?';
        return $crud->delete('room', $condition, $this->roomID);
    }

    // Delete a room by ID
    public function deleteRoom($id)
    {
        $query = "DELETE FROM room WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Get a room by ID
    public function getRoomById($id)
    {
        $query = "SELECT * FROM room WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }


    // Get all rooms
    public function getAllRooms()
    {
        $query = "SELECT * FROM room";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get all available rooms
    public function getAvailableRooms()
    {
        $query = "SELECT * FROM room WHERE isAvailable = true";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get all rooms by type
    public function getRoomsByType($type)
    {
        $query = "SELECT * FROM room WHERE type = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $type);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get all rooms by floor
    public function getRoomsByFloor($floor)
    {
        $query = "SELECT * FROM room WHERE floor = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $floor);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get all rooms by capacity
    public function getRoomsByCapacity($capacity)
    {
        $query = "SELECT * FROM room WHERE capacity = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $capacity);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get all rooms by type and floor
    public function getRoomsByTypeAndFloor($type, $floor)
    {
        $query = "SELECT * FROM room WHERE type = ? AND floor = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $type, $floor);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* get all rooms by user id
    
    public function getRoomsByUserId($userId)
    {
        $query = "SELECT * FROM room WHERE id IN (SELECT room_id FROM reservation WHERE user_id = ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    */
}

?>