<?php

require_once __DIR__ . '/../utils/crud.php';
require_once __DIR__ . '/../utils/constants.php';


use Utils\Constants;
use Utils\Crud;

class CommentModel
{
    private $conn;
    public $commentID;
    public $userID;
    public $content;
    public $createdAt;

    // Constructor
    function __construct($conn, $commentID = null, $userID = null, $content = null, $createdAt = null)
    {
        $this->conn = $conn;
        $this->commentID = $commentID;
        $this->userID = $userID;
        $this->content = $content;
        $this->createdAt = $createdAt;
    }

    // Create a new comment
    public function save()
    {
        $crud = new Crud($this->conn);
        $columns = ['userID', 'content'];
        $values = [$this->userID, $this->content];
        return $crud->create('comments', $columns, $values);
    }

    // Update a comment
    public function update()
    {
        $crud = new Crud($this->conn);
        $update = ['content' => $this->content];
        $condition = 'commentID = ?';
        return $crud->update('comments', $update, $condition, $this->commentID);
    }

    // Delete a comment
    public function delete()
    {
        $crud = new Crud($this->conn);
        $condition = 'commentID = ?';
        return $crud->delete('comments', $condition, $this->commentID);
    }

    // Get all comments
    public function getAllComments()
    {
        $crud = new Crud($this->conn);
        $result = $crud->read('comments');
        
        // Check if there are no records
        return !empty($result) ? $result : Constants::NO_RECORDS;
    }

    // Get a comment by comment ID
    public function getCommentByID($commentID)
    {
        $crud = new Crud($this->conn);
        $condition = 'commentID = ?';
        $result = $crud->read('comments', [], $condition, $commentID);

        return !empty($result) ? $result[0] : Constants::COMMENT_NOT_FOUND;
    }

    // Delete all comments
    public function deleteAllComments()
    {
        $crud = new Crud($this->conn);
        $condition = '1';
        return $crud->delete('comments', $condition);
    }

    // Get comments by userID
    public function getCommentsByUserID($userID)
    {
        $crud = new Crud($this->conn);
        $condition = 'userID = ?';
        $result = $crud->read('comments', [], $condition, $userID);

        // Check if result is not empty
        return !empty($result) ? $result : Constants::NO_RECORDS;
    }

    // Get comments by creation date
    public function getCommentsByCreatedAt($date)
    {
        $crud = new Crud($this->conn);
        $condition = 'DATE(createdAt) = ?';
        $result = $crud->read('comments', [], $condition, $date);

        // Check if result is not empty
        return !empty($result) ? $result : Constants::NO_RECORDS;
    }

    // Get the number of comments
    public function getCommentCount()
    {
        $crud = new Crud($this->conn);
        $result = $crud->read('comments', ['COUNT(*) as count']);
        return $result[0]['count'] ?? 0;
    }

    // Get the number of comments by user ID
    public function getCommentCountByUserID($userID)
    {
        $crud = new Crud($this->conn);
        $condition = 'userID = ?';
        $result = $crud->read('comments', ['COUNT(*) as count'], $condition, $userID);
        return $result[0]['count'] ?? 0;
    }
}
