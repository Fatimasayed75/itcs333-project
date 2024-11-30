<?php

require_once __DIR__ . '/../utils/crud.php';
require_once __DIR__ . '/../utils/constants.php';
require_once 'user-model.php';
require_once 'room-model.php';
require_once 'book-model.php';

use Utils\Constants;
use Utils\Crud;

class CommentModel
{
    private $conn;
    public $commentID;
    public $userID;
    public $roomID;
    public $bookingID;
    public $content;
    public $createdAt;
    public $isRead;


    private $userModel;
    private $roomModel;
    private $bookModel;

    // Constructor
    function __construct($conn, $commentID = null, $userID = null, $roomID = null, $bookingID = null, $content = null, $createdAt = null, $isRead = 0)
    {
        $this->conn = $conn;
        $this->commentID = $commentID;
        $this->userID = $userID;
        $this->roomID = $roomID;
        $this->bookingID = $bookingID;  // Set bookingID
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->isRead = $isRead;

        $this->userModel = new UserModel($conn);
        $this->roomModel = new RoomModel($conn);
        $this->bookModel = new BookModel($conn, null, null, null, null, null, null);  // Initialize the booking model
    }

    // Create a new comment
    public function save()
    {
        // Null values are not accepted
        if (empty($this->userID) || empty($this->roomID) || empty($this->content) || empty($this->bookingID)) {
            return Constants::NULL_VALUE_FOUND;
        }

        // Check if the user exists
        if ($this->userModel->getUserByID($this->userID) === Constants::USER_NOT_FOUND) {
            return Constants::USER_NOT_FOUND;
        }

        // Check if the room exists
        // if ($this->roomModel->getRoomByID($roomID) === Constants::ROOM_NOT_FOUND) {
        //     return Constants::ROOM_NOT_FOUND;
        // }

        // Check if the booking exists
        if ($this->bookModel->getBookingByID($this->bookingID) === Constants::NO_RECORDS) {
            return Constants::BOOKING_NOT_FOUND;
        }

        $crud = new Crud($this->conn);
        $columns = ['userID', 'roomID', 'bookingID', 'content', 'isRead'];
        $values = [$this->userID, $this->roomID, $this->bookingID, $this->content, 0]; // Default 'isRead' to 0
        $result = $crud->create('comments', $columns, $values);

        // Check if the comment was saved
        return $result ? $result : Constants::FAILED;
    }
    
    // Update a comment
    public function update()
    {
        // Check if the comment exists
        if ($this->getCommentByID($this->commentID) === Constants::COMMENT_NOT_FOUND) {
            return Constants::COMMENT_NOT_FOUND;
        }

        $crud = new Crud($this->conn);
        $update = ['content' => $this->content, 'isRead' => $this->isRead];
        $condition = 'commentID = ?';
        $result = $crud->update('comments', $update, $condition, $this->commentID);

        // check if the comment updated
        return $result ? Constants::SUCCESS : Constants::FAILED;
    }

    // Delete a comment
    public function delete()
    {
        // Check if the comment exists
        if ($this->getCommentByID($this->commentID) === Constants::COMMENT_NOT_FOUND) {
            return Constants::COMMENT_NOT_FOUND;
        }

        $crud = new Crud($this->conn);
        $condition = 'commentID = ?';
        $result = $crud->delete('comments', $condition, $this->commentID);

        // check if the comment deleted
        return $result ? Constants::SUCCESS : Constants::FAILED;
    }

    // Get all comments
    public function getAllComments()
    {
        $crud = new Crud($this->conn);
        $result = $crud->read('comments');
    
        // Check if there are no records
        return !empty($result) ? $result : []; 
    }
    

    // Get a comment by comment ID
    public function getCommentByID($commentID)
    {
        $crud = new Crud($this->conn);
        $condition = 'commentID = ?';
        $result = $crud->read('comments', [], $condition, $commentID);

        return !empty($result) ? $result[0] : Constants::COMMENT_NOT_FOUND;
    }

    // Get comments by roomID
    public function getCommentsByRoomID($roomID)
    {
        $crud = new Crud($this->conn);
        $condition = 'roomID = ?';
        $result = $crud->read('comments', [], $condition, $roomID);

        // Check if result is not empty
        return !empty($result) ? $result : Constants::NO_RECORDS;
    }

    // Delete all comments
    public function deleteAllComments()
    {
        $crud = new Crud($this->conn);
        $condition = '1';
        $result = $crud->delete('comments', $condition);

        // check if all comments are deleted
        return $result ? Constants::SUCCESS : Constants::FAILED;
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
        // Check if the user exists
        if ($this->userModel->getUserByID($this->userID) === Constants::USER_NOT_FOUND) {
            return Constants::USER_NOT_FOUND;
        }

        $crud = new Crud($this->conn);
        $condition = 'userID = ?';
        $result = $crud->read('comments', ['COUNT(*) as count'], $condition, $userID);
        return $result[0]['count'] ?? 0;
    }

    // Get all read comments
    public function getAllReadComments()
    {
        $crud = new Crud($this->conn);
        $condition = 'isRead = 1';
        $orderBy = 'ORDER BY createdAt DESC';
        $result = $crud->read('comments', [], $condition, null, $orderBy);

        return !empty($result) ? $result : Constants::NO_RECORDS;
    }

    // Get all unread comments
    public function getAllUnreadComments()
    {
        $crud = new Crud($this->conn);
        $condition = 'isRead = 0';
        $orderBy = 'ORDER BY createdAt DESC';
        $result = $crud->read('comments', [], $condition, null, $orderBy);

        return !empty($result) ? $result : Constants::NO_RECORDS;
    }

    public function hasAdminReply($commentID)
    {
        // Fetch replies for the comment
        $replies = (new CommentReplyModel($this->conn))->getRepliesByCommentID($commentID);

        // Check if any reply is from the admin
        foreach ($replies as $reply) {
            if ($reply['userID'] == Constants::ADMIN_USER_ID) {
                return true; // Found at least one reply from the admin
            }
        }

        return false; // No reply from the admin
    }

    public function feedbackExists($bookingID) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) 
            FROM comments c 
            JOIN bookings b ON c.roomID = b.roomID AND c.userID = b.userID
            WHERE b.bookingID = :bookingID
        ");
        $stmt->execute(['bookingID' => $bookingID]);
        return $stmt->fetchColumn() > 0;
    }
    function getUserFullName($userID) {
        // Query to get the user's first name and last name from the users table
        $query = "SELECT firstName, lastName FROM users WHERE userID = :userID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmt->execute();
    
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Return the full name
        if ($user) {
            return $user['firstName'] . ' ' . $user['lastName'];
        }
    
        return 'Unknown User';
    }

    
}
