<?php
require_once '../database/comment-reply-model.php';
require_once '../db-connection.php';
require_once '../utils/constants.php';
require_once '../utils/helpers.php';

USE Utils\Constants;

header('Content-Type: application/json');

// Get the raw POST data and decode the JSON
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['commentID'], $data['replyContent'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$commentID = $data['commentID'];
$replyContent = trim($data['replyContent']);
if (empty($replyContent)) {
    echo json_encode(['status' => 'error', 'message' => 'Reply content cannot be empty']);
    exit;
}

$userID = isAuthorized();

// Instantiate model
$replyModel = new CommentReplyModel($pdo);
$replyModel->commentID = $commentID;
$replyModel->userID = $userID;
$replyModel->replyContent = $replyContent;

// Save reply and return the result
$result = $replyModel->save();

if ($result === Constants::FAILED) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save reply']);
    exit;
} else {
    echo json_encode([
        'status' => 'success',
        'message' => 'Reply saved',
        'replyID' => $result,
        'replyContent' => $replyContent,
        'createdAt' => date('Y-m-d H:i:s') // Include timestamp
    ]);
}
