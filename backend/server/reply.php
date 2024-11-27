<?php
// Include necessary files
require_once '../database/comment-reply-model.php';
require_once '../database/comment-model.php';
require_once '../db-connection.php';
require_once '../utils/constants.php'; // For Constants

USE Utils\Constants;

session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['active-user']) || $_SESSION['email'] !== Constants::ADMIN_EMAIL) {
    header("Location: login.php");
    exit();
}

// Instantiate models
$commentReplyModel = new CommentReplyModel($conn);

// Handle form submission to reply to a comment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $commentID = $_POST['commentID'];
    $userID = $_SESSION['active-user']; // Admin ID from session
    $replyContent = $_POST['replyContent'];

    // Set up the reply model to save the new reply
    $commentReplyModel->commentID = $commentID;
    $commentReplyModel->userID = $userID;
    $commentReplyModel->replyContent = $replyContent;

    // Save the reply (no parentReplyID needed)
    $result = $commentReplyModel->save();

    // If successful, redirect back to the notification page
    if ($result === Constants::SUCCESS) {
        header("Location: notification.php"); // Redirect to the notification page to see the reply
        exit();
    } else {
        // Handle error (optional)
        echo "<p>Error: Could not save reply.</p>";
    }
}
?>
