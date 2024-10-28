<?php

require_once __DIR__ . '/../utils/crud.php';
require_once __DIR__ . '/../utils/constants.php';

use Utils\Constants;
use Utils\Crud;

class SessionModel
{
    private $conn;
    public $sessionID;
    public $userID;
    public $token;
    public $createdAt;
    public $expiresAt;
    public $isActive;

    // Constructor
    function __construct($conn, $sessionID = null, $userID = null, $token = null, $createdAt = null, $expiresAt = null, $isActive = 1)
    {
        $this->conn = $conn;
        $this->sessionID = $sessionID;
        $this->userID = $userID;
        $this->token = $token;
        $this->createdAt = $createdAt;
        $this->expiresAt = $expiresAt;
        $this->isActive = $isActive;
    }

    // Create a new session
    public function save()
    {
        // Null values are not accepted
        if (empty($this->userID) || empty($this->token)) {
            return Constants::NULL_VALUE_FOUND;
        }

        $crud = new Crud($this->conn);
        $columns = ['userID', 'token', 'expiresAt', 'isActive'];
        $values = [$this->userID, $this->token, $this->expiresAt, $this->isActive];
        $result = $crud->create('session', $columns, $values);

        // Check if the session saved
        return $result ? $result : Constants::FAILED;
    }

    // Update an existing session
    public function update()
    {
        // Check if the session exists
        if ($this->getSessionByID($this->sessionID) === Constants::SESSION_NOT_FOUND) {
            return Constants::SESSION_NOT_FOUND;
        }

        $crud = new Crud($this->conn);
        $update = [
            'token' => $this->token,
            'expiresAt' => $this->expiresAt,
            'isActive' => $this->isActive
        ];
        $condition = 'sessionID = ?';
        $result = $crud->update('session', $update, $condition, $this->sessionID);

        // Check if the session updated
        return $result ? Constants::SUCCESS : Constants::FAILED;
    }

    // Delete a session
    public function delete()
    {
        // Check if the session exists
        if ($this->getSessionByID($this->sessionID) === Constants::SESSION_NOT_FOUND) {
            return Constants::SESSION_NOT_FOUND;
        }

        $crud = new Crud($this->conn);
        $condition = 'sessionID = ?';
        $result = $crud->delete('session', $condition, $this->sessionID);

        // Check if the session deleted
        return $result ? Constants::SUCCESS : Constants::FAILED;
    }

    // Get a session by session ID
    public function getSessionByID($sessionID)
    {
        $crud = new Crud($this->conn);
        $condition = 'sessionID = ?';
        $result = $crud->read('session', [], $condition, $sessionID);

        return !empty($result) ? $result[0] : Constants::SESSION_NOT_FOUND;
    }

    // Get all active sessions
    public function getAllActiveSessions()
    {
        $crud = new Crud($this->conn);
        $condition = 'isActive = 1';
        $result = $crud->read('session', [], $condition);

        // Check if there are no records
        return !empty($result) ? $result : Constants::NO_RECORDS;
    }

    // Get sessions by user ID
    public function getSessionsByUserID($userID)
    {
        $crud = new Crud($this->conn);
        $condition = 'userID = ?';
        $result = $crud->read('session', [], $condition, $userID);

        return !empty($result) ? $result : Constants::NO_RECORDS;
    }

    // Expire session "convert the status of the session from active to not active"
    public function expireSession()
    {
        $crud = new Crud($this->conn);
        $condition = 'sessionID = ?';
        $update = ['isActive' => 0];
        return $crud->update('session', $update, $condition, $this->sessionID);
    }
}
