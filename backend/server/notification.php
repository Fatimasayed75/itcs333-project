<?php
// Include necessary files
require_once '../database/comment-model.php';
require_once '../database/comment-reply-model.php';
require_once '../database/user-model.php';
require_once '../db-connection.php';
require_once '../utils/helpers.php';
require_once '../utils/constants.php'; // Where you have ADMIN_EMAIL

USE Utils\Constants;
// Start session
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['userID']) || $_SESSION['email'] !== Constants::ADMIN_EMAIL) {
    header("Location: login.php");
    exit();
}

// Instantiate models
$commentModel = new CommentModel($conn);
$commentReplyModel = new CommentReplyModel($conn);

// Fetch all comments made by users
$comments = $commentModel->getAllComments();

// Function to fetch replies to a specific comment
function getRepliesForComment($commentID, $commentReplyModel) {
    return $commentReplyModel->getRepliesByCommentID($commentID);
}

