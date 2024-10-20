<?php

require_once __DIR__ . '/../utils/crud.php';
require_once __DIR__ . '/../utils/constants.php';

use Utils\Constants;
use Utils\Crud;

class UserModel
{
    private $conn;
    public $userID;
    public $email;
    public $userPassword;
    public $role;
    public $profilePic;

    // Constructor
    function __construct($conn, $userID = null, $email = null, $userPassword = null, $role = null, $profilePic = null)
    {
        $this->conn = $conn;
        $this->userID = $userID;
        $this->email = $email;
        $this->userPassword = $userPassword;
        $this->role = $role;
        $this->profilePic = $profilePic;
    }

    // Save a new user
    public function save()
    {
        $crud = new Crud($this->conn);
        $columns = ['email', 'userPassword', 'role', 'profilePic'];
        $values = [$this->email, $this->userPassword, $this->role, $this->profilePic];
        return $crud->create('users', $columns, $values);
    }

    // Update a user
    public function update()
    {
        $crud = new Crud($this->conn);
        $update = ['email' => $this->email, 'userPassword' => $this->userPassword, 'role' => $this->role, 'profilePic' => $this->profilePic];
        $condition = 'userID = ?';
        return $crud->update('users', $update, $condition, $this->userID);
    }

    // Delete a user
    public function delete()
    {
        $crud = new Crud($this->conn);
        $condition = 'userID = ?';
        return $crud->delete('users', $condition, $this->userID);
    }

    // Get all users
    public function getAllUsers()
    {
        $crud = new Crud($this->conn);
        $result = $crud->read('users');
        return !empty($result) ? $result : Constants::NO_RECORDS;
    }

    // Get a user by user ID
    public function getUserByID($userID)
    {
        $crud = new Crud($this->conn);
        $condition = 'userID = ?';
        $result = $crud->read('users', [], $condition, $userID);
        return !empty($result) ? $result[0] : Constants::USER_NOT_FOUND;
    }

    // Delete a user by email
    public function deleteUserByEmail($email)
    {
        $crud = new Crud($this->conn);
        $condition = 'email = ?';
        return $crud->delete('users', $condition, $email);
    }

    // Get the number of users
    public function getUserCount()
    {
        $crud = new Crud($this->conn);
        $result = $crud->read('users', ['COUNT(*) as count']);
        return $result[0]['count'] ?? 0;
    }
}
?>