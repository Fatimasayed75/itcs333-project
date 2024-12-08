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
    // Save a new user
    public function save()
    {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO users (email, password, firstName, lastName, role)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
            
            $stmt->execute([
                $this->email,
                $hashedPassword,
                $this->firstName,
                $this->lastName,
                $this->role
            ]);
            
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error saving user: " . $e->getMessage());
            throw new Exception("Failed to create user account");
        }
    }


    // Update a user
    public function isEmailRegistered($email)
    {
        $crud = new Crud($this->conn);
        $condition = 'email = ?';
        return !empty($crud->read('users', ['email'], $condition, $email));
    }

    public function update($userID, $firstName, $lastName, $email, $fileId = null)
    {
        try {
            $sql = "UPDATE users SET firstName = ?, lastName = ?, email = ?";
            $params = [$firstName, $lastName, $email];

            if ($fileId !== null) {
                $sql .= ", profilePic = ?";
                $params[] = $fileId;
            }

            $sql .= " WHERE userID = ?";
            $params[] = $userID;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error updating user: " . $e->getMessage());
            throw new Exception("Failed to update user profile");
        }
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
        try {
            $stmt = $this->conn->prepare("
                SELECT u.*, f.id as file_id, f.mime_type, fc.file_content
                FROM users u
                LEFT JOIN files f ON u.profilePic = f.id
                LEFT JOIN file_contents fc ON f.id = fc.file_id
            ");
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($results)) {
                return Constants::NO_RECORDS;
            }

            // Format profile picture data for each user
            foreach ($results as &$result) {
                if ($result['file_id']) {
                    $result['profilePicData'] = [
                        'mime_type' => $result['mime_type'],
                        'file_content' => $result['file_content']
                    ];
                }
            }
            
            return $results;
        } catch (PDOException $e) {
            error_log("Error fetching users: " . $e->getMessage());
            throw new Exception("Failed to fetch users data");
        }
    }

    // Get a user by user ID
    // Get a user by user ID
    public function getUserByID($userID)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT u.*, f.id as file_id, f.mime_type, fc.file_content
                FROM users u
                LEFT JOIN files f ON u.profilePic = f.id
                LEFT JOIN file_contents fc ON f.id = fc.file_id
                WHERE u.userID = ?
            ");
            $stmt->execute([$userID]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                return Constants::USER_NOT_FOUND;
            }

            // Format the profile picture data if it exists
            if ($result['file_id']) {
                $result['profilePicData'] = [
                    'mime_type' => $result['mime_type'],
                    'file_content' => $result['file_content']
                ];
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error fetching user: " . $e->getMessage());
            throw new Exception("Failed to fetch user data");
        }
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

    public function getTotalUsers()
    {
        $query = "SELECT COUNT(*) AS totalUsers FROM users";
        $stmt = $this->conn->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['totalUsers'];
    }
}
