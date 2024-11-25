<?php

require_once __DIR__ . '/../utils/crud.php';
require_once __DIR__ . '/../utils/constants.php';
require_once 'comment-model.php';
require_once 'user-model.php';
require_once 'room-model.php';


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

    private $commentModel;
    private $userModel;
    private $roomModel;

    // Constructor
    function __construct($conn, $replyID = null, $commentID = null, $userID = null, $replyContent = null, $createdAt = null)
    {
        $this->conn = $conn;
        $this->replyID = $replyID;
        $this->commentID = $commentID;
        $this->userID = $userID;
        $this->replyContent = $replyContent;
        $this->createdAt = $createdAt;

         $this->commentModel = new CommentModel($conn);
         $this->userModel = new UserModel($conn);
         $this->roomModel = new RoomModel($conn);
    }

    // Create a new reply
    public function save()
    {
        // Null values are not accepted
        if (empty($this->commentID) || empty($this->userID) || empty($this->replyContent)) {
            return Constants::NULL_VALUE_FOUND;
        }

        // Check if the comment exists
        if ($this->commentModel->getCommentByID($this->commentID) === Constants::COMMENT_NOT_FOUND) {
            return Constants::COMMENT_NOT_FOUND;
        }

        // Check if the user exists
        if ($this->userModel->getUserByID($this->userID) === Constants::USER_NOT_FOUND) {
            return Constants::USER_NOT_FOUND;
        }

        $crud = new Crud($this->conn);
        $columns = ['commentID', 'userID', 'replyContent'];
        $values = [$this->commentID, $this->userID, $this->replyContent];
        $result = $crud->create('comment_reply', $columns, $values);

        // Check if the reply was saved
        return $result ? $result : Constants::FAILED;
    }

    // Update a reply
    public function update()
    {
        // Check if the reply exists
        if ($this->getReplyByID($this->replyID) === Constants::REPLY_NOT_FOUND) {
            return Constants::REPLY_NOT_FOUND;
        }

        $crud = new Crud($this->conn);
        $update = ['replyContent' => $this->replyContent];
        $condition = 'replyID = ?';
        $result = $crud->update('comment_reply', $update, $condition, $this->replyID);

        // Check if the reply was updated
        return $result ? Constants::SUCCESS : Constants::FAILED;
    }

    // Delete a reply
    public function delete()
    {
        // Check if the reply exists
        if ($this->getReplyByID($this->replyID) === Constants::REPLY_NOT_FOUND) {
            return Constants::REPLY_NOT_FOUND;
        }

        $crud = new Crud($this->conn);
        $condition = 'replyID = ?';
        $result = $crud->delete('comment_reply', $condition, $this->replyID);

        // Check if the reply was deleted
        return $result ? Constants::SUCCESS : Constants::FAILED;
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
        // Check if the comment exists
        if ($this->commentModel->getCommentByID($commentID) === Constants::COMMENT_NOT_FOUND) {
            return Constants::COMMENT_NOT_FOUND;
        }

        $crud = new Crud($this->conn);
        $condition = 'commentID = ?';
        $result = $crud->read('comment_reply', [], $condition, $commentID);

        // Check if the result is not empty
        return !empty($result) ? $result : Constants::NO_RECORDS;
    }

    // Get replies by user ID
    public function getRepliesByUserID($userID)
    {
        // Check if the user exists
        if ($this->userModel->getUserByID($userID) === Constants::USER_NOT_FOUND) {
            return Constants::USER_NOT_FOUND;
        }

        $crud = new Crud($this->conn);
        $condition = 'userID = ?';
        $result = $crud->read('comment_reply', [], $condition, $userID);

        // Check if the result is not empty
        return !empty($result) ? $result : Constants::NO_RECORDS;
    }

    // Get replies by creation date
    public function getRepliesByCreatedAt($date)
    {
        $crud = new Crud($this->conn);
        $condition = 'DATE(createdAt) = ?';
        $result = $crud->read('comment_reply', [], $condition, $date);

        // Check if the result is not empty
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
        // Check if the comment exists
        if ($this->commentModel->getCommentByID($commentID) === Constants::COMMENT_NOT_FOUND) {
            return Constants::COMMENT_NOT_FOUND;
        }

        $crud = new Crud($this->conn);
        $condition = 'commentID = ?';
        $result = $crud->read('comment_reply', ['COUNT(*) as count'], $condition, $commentID);
        return $result[0]['count'] ?? 0;
    }

    // Delete all replies for a specific comment
    public function deleteAllRepliesByCommentID($commentID)
    {
        // Check if the comment exists
        if ($this->commentModel->getCommentByID($commentID) === Constants::COMMENT_NOT_FOUND) {
            return Constants::COMMENT_NOT_FOUND;
        }

        $crud = new Crud($this->conn);
        $condition = 'commentID = ?';
        $result = $crud->delete('comment_reply', $condition, $commentID);

        // Check if all replies are deleted
        return $result ? Constants::SUCCESS : Constants::FAILED;
    }

    // Delete all replies
    public function deleteAllReplies()
    {
        $crud = new Crud($this->conn);
        $condition = '1';
        $result = $crud->delete('comment_reply', $condition);

        // Check if all replies are deleted
        return $result ? Constants::SUCCESS : Constants::FAILED;
    }

    // Delete replies by room ID
    public function deleteRepliesByRoomID($roomID)
    {
        // Check if the room exists
        // if ($this->roomModel->getRoomByID($roomID) === Constants::ROOM_NOT_FOUND) {
        //     return Constants::ROOM_NOT_FOUND;
        // }

        $query = "DELETE comment_reply 
                  FROM comment_reply 
                  INNER JOIN comments ON comment_reply.commentID = comments.commentID 
                  WHERE comments.roomID = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $roomID, PDO::PARAM_INT);
        $result = $stmt->execute();

        // Check if replies were deleted
        return $result ? Constants::SUCCESS : Constants::FAILED;
    }
}