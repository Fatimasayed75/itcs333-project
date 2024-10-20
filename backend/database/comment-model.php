<?php

require_once __DIR__ . '/../utils/crud.php';

use Utils\Crud;
// use Utils\Constants;

class CommentModel
{
    private $conn;
    public $commentID;
    public $userID;
    public $content;
    public $createdAt;

    // constructor
    function __construct($conn, $commentID = null, $userID = null, $content = null, $createdAt = null)
    {
        $this->conn = $conn;
        $this->commentID = $commentID;
        $this->userID = $userID;
        $this->content = $content;
        $this->createdAt = $createdAt;
    }

    // create a new comment
    public function save()
    {
        $crud = new Crud($this->conn);
        $columns = ['userID', 'content'];
        $values = [$this->userID, $this->content];
        return $crud->create('comments', $columns, $values);
    }

    // update a comment
    public function update()
    {
        $crud = new Crud($this->conn);
        $update = ['content' => $this->content];
        $condition = 'commentID = ?';
        return $crud->update('comments', $update, $condition, $this->commentID);
    }

    // delete a comment
    public function delete()
    {
        $curd = new Crud($this->conn);
        $condition = 'commentID = ?';
        return $curd->delete('comments', $condition, $this->commentID);
    }

    // Get a comment by its ID
    // public function getCommentByID($commentID)
    // {
    //     $crud = new Crud($this->conn);
    //     $condition = 'commentID = ?';
    //     $result = $crud->read('comments', [], $condition, $commentID);

    //     if (!empty($result)) {

    //         foreach ($result[0] as $key => $value) {
    //             $this->{$key} = $value;
    //         }

    //         // $row = $result[0];
    //         // $this->commentID = $row['commentID'];
    //         // $this->userID = $row['userID'];
    //         // $this->content = $row['content'];
    //         // $this->createdAt = $row['createdAt'];
    //         return $this;
    //     }
    //     return Constants::COMMENT_NOT_FOUND;
    // }
}
