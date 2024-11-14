<?php

// comment this in case we won't use the Crud class
require_once __DIR__ . '/../utils/crud.php';
use Utils\Crud;

class EquipmentModel
{

    private $conn;
    private $crud;

    public $equipmentID;
    public $roomID;
    public $equipmentName;

    public function __construct($dbConnection, $equipmentID = null, $equipmentName = null, $roomID = null)
    {
        $this->conn = $dbConnection;
        $this->crud = new Crud($dbConnection);
        $this->equipmentID = $equipmentID;
        $this->roomID = $roomID;
        $this->equipmentName = $equipmentName;
    }

    // Save, function to save a equipment to the database
    public function save()
    {
        $crud = new Crud($this->conn);
        $columns = ['equipmentID', 'equipmentName', 'roomID'];
        $values = [$this->equipmentID, $this->equipmentName, $this->roomID];
        return $crud->create('equipment', $columns, $values);
    }

    // Update, function to update a equipment to the database
    public function update()
    {
        $crud = new Crud($this->conn);
        $updates = ['equipmentName' => $this->equipmentName, 'roomID' => $this->roomID];
        $condition = 'equipmentID = ?';
        return $crud->update('equipment', $updates, $condition, $this->equipmentID);
    }

    // Delete, function to delete a equipment from the database
    public function delete()
    {
        $crud = new Crud($this->conn);
        $condition = 'equipmentID = ?';
        return $crud->delete('equipment', $condition, $this->equipmentID);
    }

    // Get all equipments
    public function getAllEquipments() {
        return $this->crud->read('equipment');
    }

    // Get a equipment by ID
    public function getEquipmentById($id) {
        return $this->crud->read('equipment', ['equipmentID' => $id]);
    }

    // Get equipments by room ID
    public function getEquipmentsByRoomID($roomID) {
        return $this->crud->read('equipment', ['roomID' => $roomID]);
    }

    // Get equipments by name
    public function getEquipmentsByName($name) {
        return $this->crud->read('equipment', ['equipmentName' => $name]);
    }
}