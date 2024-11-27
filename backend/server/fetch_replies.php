<?php
require_once '../database/comment-reply-model.php';
require_once '../db-connection.php';

USE Utils\Constants;


// Instantiate the CommentReplyModel
$commentReplyModel = new CommentReplyModel($pdo);

// Check if the 'commentID' parameter is passed in the GET request
if (isset($_GET['commentID'])) {
    $commentID = $_GET['commentID'];

    // Fetch replies for the specified comment
    $replies = $commentReplyModel->getRepliesByCommentID($commentID);

    // Prepare the response data
    $response = [];
    if ($replies) {
        foreach ($replies as $reply) {
            $response[] = [
                'replyContent' => $reply['replyContent'],
                'createdAt' => $reply['createdAt'],
                'userRole' => ($reply['userID'] == Constants::ADMIN_USER_ID) ? 'admin' : 'user' // Determine if the reply is from an admin
            ];
        }
    }

    // Return the data as a JSON response
    echo json_encode(['replies' => $response]);
} else {
    // If no 'commentID' is provided, return an error
    echo json_encode(['error' => 'No comment ID provided']);
}
?>
