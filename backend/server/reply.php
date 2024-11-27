<?php
// Include necessary files
require_once '../../../backend/database/comment-reply-model.php';
require_once '../../../backend/database/user-model.php';
require_once '../../../backend/db-connection.php';
require_once '../../../backend/utils/constants.php';

USE Utils\Constants;

// Check if the user is authorized
$id = isAuthorized();

// Get the comment ID and reply content from POST request
$commentID = $_POST['commentID'] ?? null;
$replyContent = $_POST['replyContent'] ?? null;

header('Content-Type: application/json'); // Ensure JSON response

if ($commentID && $replyContent) {
    // Instantiate the model
    $commentReplyModel = new CommentReplyModel($pdo);

    // Create a new reply
    $commentReplyModel->commentID = $commentID;
    $commentReplyModel->userID = $id;
    $commentReplyModel->replyContent = $replyContent;

    // Save the reply and get the result
    $result = $commentReplyModel->save();

    if ($result === Constants::SUCCESS) {
        echo json_encode(['success' => true, 'message' => 'Reply posted successfully']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save the reply']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}
