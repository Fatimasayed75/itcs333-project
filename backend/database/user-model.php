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
    public $password;
    public $firstName;
    public $lastName;
    public $role;
    public $profilePic;

    // Constructor
    function __construct($conn, $userID = null, $email = null, $password = null, $firstName = null, $lastName = null, $role = null, $profilePic = null)
    {
        $this->conn = $conn;
        $this->userID = $userID;
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->role = $role;
        $this->profilePic = $profilePic;
    }

    // Save a new user
    public function save()
    {
        $crud = new Crud($this->conn);
        $columns = ['email', 'password', 'firstName', 'lastName', 'role', 'profilePic'];
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        $this->password = $hashedPassword;
        $values = [$this->email, $this->password, $this->firstName, $this->lastName, $this->role, $this->profilePic];
        $crud->create('users', $columns, $values);
        return $this->conn->lastInsertId();
    }

    // Update a user
    public function isEmailRegistered($email)
    {
        $crud = new Crud($this->conn);
        $condition = 'email = ?';
        return !empty($crud->read('users', ['email'], $condition, $email));
    }

    public function update($userID, $firstName, $lastName, $email, $profilePic)
    {
        $crud = new Crud($this->conn);
        $updates = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'profilePic' => $profilePic
        ];
        $condition = 'userID = ?';
        return $crud->update('users', $updates, $condition, $userID);
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

    // Get a user by email
    public function getUserByEmail($email)
    {
        $crud = new Crud($this->conn);
        $condition = 'email = ?';
        $result = $crud->read('users', [], $condition, $email);
        return !empty($result) ? $result[0] : null;
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

    function getNewUsersThisMonth() {
        // SQL query to count new users this month
        $query = "
            SELECT COUNT(*) AS new_users
            FROM users
            WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
            AND YEAR(created_at) = YEAR(CURRENT_DATE())
        ";
    
        // Prepare and execute the query
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    
        // Fetch the result and return the count of new users
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['new_users'] ?? 0; // Return the number of new users or 0 if no data
    }

    
}
