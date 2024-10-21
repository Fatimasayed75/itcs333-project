<?php

require_once __DIR__ . '/../utils/crud.php';
require_once __DIR__ . '/../utils/constants.php';

use Utils\Constants;
use Utils\Crud;

class CommentReplyModel
{
    private $conn;
    public $replyID;
    public $commentID;
    public $userID;
    public $replyContent;
    public $createdAt;

    // Constructor
    function __construct($conn, $replyID = null, $commentID = null, $userID = null, $replyContent = null, $createdAt = null)
    {
        $this->conn = $conn;
        $this->replyID = $replyID;
        $this->commentID = $commentID;
        $this->userID = $userID;
        $this->replyContent = $replyContent;
        $this->createdAt = $createdAt;
    }

    // Create a new reply
    public function save()
    {
        // null values are not accepted
        if (empty($this->commentID) || empty($this->userID) || empty($this->replyContent)) {
            return Constants::NULL_VALUE_FOUND;
        }

        $crud = new Crud($this->conn);
        $columns = ['commentID', 'userID', 'replyContent'];
        $values = [$this->commentID, $this->userID, $this->replyContent];
        return $crud->create('comment_reply', $columns, $values);
    }

    // Update a reply
    public function update()
    {
        $crud = new Crud($this->conn);
        $update = ['replyContent' => $this->replyContent];
        $condition = 'replyID = ?';
        return $crud->update('comment_reply', $update, $condition, $this->replyID);
    }

    // Delete a reply
    public function delete()
    {
        $crud = new Crud($this->conn);
        $condition = 'replyID = ?';
        return $crud->delete('comment_reply', $condition, $this->replyID);
    }

    // Get all replies
    public function getAllReplies()
    {
        $crud = new Crud($this->conn);
        $result = $crud->read('comment_reply');

        // Check if there are no records
        return !empty($result) ? $result : Constants::NO_RECORDS;
    }

    // Get a reply by reply ID
    public function getReplyByID($replyID)
    {
        $crud = new Crud($this->conn);
        $condition = 'replyID = ?';
        $result = $crud->read('comment_reply', [], $condition, $replyID);

        return !empty($result) ? $result[0] : Constants::REPLY_NOT_FOUND;
    }

    // Get replies by comment ID
    public function getRepliesByCommentID($commentID)
    {
        $crud = new Crud($this->conn);
        $condition = 'commentID = ?';
        $result = $crud->read('comment_reply', [], $condition, $commentID);

        // Check if result is not empty
        return !empty($result) ? $result : Constants::NO_RECORDS;
    }

    // Get replies by user ID
    public function getRepliesByUserID($userID)
    {
        $crud = new Crud($this->conn);
        $condition = 'userID = ?';
        $result = $crud->read('comment_reply', [], $condition, $userID);

        // Check if result is not empty
        return !empty($result) ? $result : Constants::NO_RECORDS;
    }

    // Get replies by creation date
    public function getRepliesByCreatedAt($date)
    {
        $crud = new Crud($this->conn);
        $condition = 'DATE(createdAt) = ?';
        $result = $crud->read('comment_reply', [], $condition, $date);

        // Check if result is not empty
        return !empty($result) ? $result : Constants::NO_RECORDS;
    }

    // Get the number of replies
    public function getReplyCount()
    {
        $crud = new Crud($this->conn);
        $result = $crud->read('comment_reply', ['COUNT(*) as count']);
        return $result[0]['count'] ?? 0;
    }

    // Get the number of replies by comment ID
    public function getReplyCountByCommentID($commentID)
    {
        $crud = new Crud($this->conn);
        $condition = 'commentID = ?';
        $result = $crud->read('comment_reply', ['COUNT(*) as count'], $condition, $commentID);
        return $result[0]['count'] ?? 0;
    }

    // Delete all replies for a specific comment
    public function deleteAllRepliesByCommentID($commentID)
    {
        $crud = new Crud($this->conn);
        $condition = 'commentID = ?';
        return $crud->delete('comment_reply', $condition, $commentID);
    }

    // Delete all replies
    public function deleteAllReplies()
    {
        $crud = new Crud($this->conn);
        $condition = '1';
        return $crud->delete('comment_reply', $condition);
    }


    // Delete replies by room ID
    public function deleteRepliesByRoomID($roomID)
    {
        $query = "DELETE comment_reply 
              FROM comment_reply 
              INNER JOIN comments ON comment_reply.commentID = comments.commentID 
              WHERE comments.roomID = ?";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$roomID]);
    }
}
