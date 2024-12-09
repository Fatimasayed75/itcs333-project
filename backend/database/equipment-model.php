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
    public $Quantity;

    public function __construct($dbConnection, $equipmentID = null, $Quantity = null, $roomID = null)
    {
        $this->conn = $dbConnection;
        $this->crud = new Crud($dbConnection);
        $this->equipmentID = $equipmentID;
        $this->roomID = $roomID;
        $this->Quantity = $Quantity;
    }

    
}